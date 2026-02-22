<?php

namespace Tests\Unit\Calculation;

use App\Enums\MunicipalityTypeEnum;
use App\Models\Municipality;
use App\Models\PPMIApplication;
use App\Models\User;
use App\Traits\CalculationPPMIApplicationTrait;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for PPMI application scoring.
 *
 * Tests all 5 municipality types (MO, MR, GP, GO, SP) with various
 * boundary conditions to ensure scoring calculations remain correct
 * during migration.
 */
class CalculationPPMITest extends TestCase
{
    use CalculationPPMIApplicationTrait;

    /**
     * Create a mock PPMIApplication with specified attributes and relations.
     */
    private function makePPMIModel(array $attrs = [], array $relations = []): PPMIApplication
    {
        $model = $this->createMock(PPMIApplication::class);

        $defaults = [
            'funds_municipal' => 0,
            'funds_individuals' => 0,
            'funds_legal_entities' => 0,
            'funds_republic' => 0,
            'population_size' => 100,
            'population_size_settlement' => 1000,
            'population_size_in_congregation' => 50,
            'population_in_project_implementation' => '',
            'population_in_project_provision' => '',
        ];

        $merged = array_merge($defaults, $attrs);

        $model->method('__get')->willReturnCallback(function ($name) use ($merged, $relations) {
            if (isset($relations[$name])) {
                return $relations[$name];
            }
            return $merged[$name] ?? null;
        });

        return $model;
    }

    /**
     * Create a mock User with municipality of given type.
     */
    private function makeUser(string $municipalityType, bool $isDistrictPlusGp = false): User
    {
        $municipality = $this->createMock(Municipality::class);
        $municipality->method('__get')->willReturnCallback(function ($name) use ($municipalityType, $isDistrictPlusGp) {
            return match ($name) {
                'type' => $municipalityType,
                'name' => 'Test Municipality',
                'is_district_plus_gp' => $isDistrictPlusGp,
                'parent' => null,
                default => null,
            };
        });

        $user = $this->createMock(User::class);
        $user->method('__get')->willReturnCallback(function ($name) use ($municipality) {
            return match ($name) {
                'municipality' => $municipality,
                default => null,
            };
        });

        return $user;
    }

    /**
     * Build model with user attached for getCalculation routing.
     */
    private function makeFullModel(
        string $municipalityType,
        array $attrs = [],
        array $relations = [],
        bool $isDistrictPlusGp = false
    ): PPMIApplication {
        $user = $this->makeUser($municipalityType, $isDistrictPlusGp);
        $emptyCollection = new Collection();

        $defaultRelations = [
            'user' => $user,
            'operating_and_maintenance_costs' => $emptyCollection,
            'participation_population_in_implementation_project' => $emptyCollection,
            'public_participation_in_operation_facility' => $emptyCollection,
            'questionnaires' => $emptyCollection,
            'mass_media' => $emptyCollection,
            'project_implementation_provides_informational_support' => $emptyCollection,
        ];

        $mergedRelations = array_merge($defaultRelations, $relations);
        $mergedAttrs = array_merge(['user' => $user], $attrs);

        $model = $this->createMock(PPMIApplication::class);

        $model->method('__get')->willReturnCallback(function ($name) use ($mergedAttrs, $mergedRelations) {
            if (isset($mergedRelations[$name])) {
                return $mergedRelations[$name];
            }
            $defaults = [
                'funds_municipal' => 0,
                'funds_individuals' => 0,
                'funds_legal_entities' => 0,
                'funds_republic' => 0,
                'population_size' => 100,
                'population_size_settlement' => 1000,
                'population_size_in_congregation' => 50,
                'population_in_project_implementation' => '',
                'population_in_project_provision' => '',
            ];
            $merged = array_merge($defaults, $mergedAttrs);
            return $merged[$name] ?? null;
        });

        return $model;
    }

    // ======================================================================
    // getProjectCost tests
    // ======================================================================

    public function test_project_cost_sums_all_fund_sources(): void
    {
        $model = $this->makePPMIModel([
            'funds_municipal' => 100000,
            'funds_individuals' => 20000,
            'funds_legal_entities' => 30000,
            'funds_republic' => 250000,
        ]);

        $this->assertEquals(400000, $this->getProjectCost($model));
    }

    public function test_project_cost_zero_when_all_zero(): void
    {
        $model = $this->makePPMIModel();
        $this->assertEquals(0, $this->getProjectCost($model));
    }

    // ======================================================================
    // MO (Municipal District) calculation tests
    // ======================================================================

    public function test_mo_returns_zero_when_project_cost_is_zero(): void
    {
        $model = $this->makeFullModel(MunicipalityTypeEnum::MO->value);
        $this->assertEquals(0, $this->getMOCalculation($model));
    }

    public function test_mo_max_score_with_high_cofunding(): void
    {
        // MO: municipal base is 10%, so 26% municipal = 16pp excess → 100*0.2=20
        // individuals+legal = 15% → 100*0.3=30
        // population 60% → 100*0.05=5
        // congregation > 5% of < 4000 → 100*0.10=10
        // + all binary criteria met → 10+10+5+5+5 = 35
        // Total: 20+30+5+10+10+10+5+5+5 = 100

        $maintenanceCost = new Collection([
            (object)['field3' => 'something', 'field4' => '', 'field5' => '', 'field6' => ''],
        ]);

        $model = $this->makeFullModel(MunicipalityTypeEnum::MO->value, [
            'funds_municipal' => 260000,
            'funds_individuals' => 100000,
            'funds_legal_entities' => 50000,
            'funds_republic' => 590000,
            'population_size' => 600,
            'population_size_settlement' => 1000,
            'population_size_in_congregation' => 60,
            'population_in_project_implementation' => 'Да',
            'population_in_project_provision' => 'Да',
        ], [
            'operating_and_maintenance_costs' => $maintenanceCost,
            'questionnaires' => new Collection(['file1']),
            'mass_media' => new Collection(['file1']),
            'participation_population_in_implementation_project' => new Collection(),
            'public_participation_in_operation_facility' => new Collection(),
            'project_implementation_provides_informational_support' => new Collection(),
        ]);

        $result = $this->getMOCalculation($model);
        $this->assertEquals(100, $result);
    }

    public function test_mo_minimum_score_with_low_values(): void
    {
        // All funding below thresholds, empty relations, no binary criteria
        $model = $this->makeFullModel(MunicipalityTypeEnum::MO->value, [
            'funds_municipal' => 50000,  // 5% of 1M → -5pp excess (below 0.1)
            'funds_individuals' => 5000,  // 0.5% < 1%
            'funds_legal_entities' => 0,
            'funds_republic' => 945000,
            'population_size' => 40,     // 4% ≤ 5%
            'population_size_settlement' => 1000,
            'population_size_in_congregation' => 15, // 1.5% ≤ 2%
        ]);

        $result = $this->getMOCalculation($model);

        // significantPercentage = (50000/1000000*100)-10 = -5 → 0
        // levelCoFinancing = 5000/1000000*100 = 0.5 → 0
        // specificShare = 40/1000*100 = 4% → 40*0.05 = 2
        // congregation = 15/1000*100 = 1.5% ≤ 2% → 25*0.10 = 2.5
        // all binary = 0
        // Total = 0 + 0 + 2 + 2.5 + 0 + 0 + 0 + 0 + 0 = 4.5
        $this->assertEquals(4.5, $result);
    }

    // ======================================================================
    // MR (Municipal Region) calculation tests
    // ======================================================================

    public function test_mr_returns_zero_when_project_cost_is_zero(): void
    {
        $model = $this->makeFullModel(MunicipalityTypeEnum::MR->value);
        $this->assertEquals(0, $this->getMRCalculation($model));
    }

    public function test_mr_municipal_base_is_20_percent(): void
    {
        // MR uses 20% base for municipal excess calculation (vs 10% for MO/SP)
        $model = $this->makeFullModel(MunicipalityTypeEnum::MR->value, [
            'funds_municipal' => 250000,   // 25% of 1M → 5pp excess
            'funds_individuals' => 100000, // 10% (with max=5 from isDistrictPlusGp=false)
            'funds_legal_entities' => 0,
            'funds_republic' => 650000,
            'population_size' => 200,
            'population_size_settlement' => 1000,
            'population_size_in_congregation' => 30,
        ]);

        $result = $this->getMRCalculation($model);

        // significantPercentage = (250000/1000000*100)-20 = 5.0 → 40*0.2=8
        // levelCoFinancing = 100000/1000000*100 = 10.0 → 80*0.3=24
        // specificShare = 200/1000*100 = 20% → 60*0.05=3
        // congregation = 30/1000*100 = 3% (settlement < 4000) → 50*0.10=5
        // binary: all 0
        // Total = 8 + 24 + 3 + 5 + 0 + 0 + 0 + 0 + 0 = 40
        $this->assertEquals(40, $result);
    }

    // ======================================================================
    // GP (Urban Settlement) calculation tests
    // ======================================================================

    public function test_gp_returns_zero_when_project_cost_is_zero(): void
    {
        $model = $this->makeFullModel(MunicipalityTypeEnum::GP->value);
        $this->assertEquals(0, $this->getGPCalculation($model));
    }

    public function test_gp_congregation_brackets_for_large_settlement(): void
    {
        // Settlement > 4000 → uses absolute numbers for congregation
        $model = $this->makeFullModel(MunicipalityTypeEnum::GP->value, [
            'funds_municipal' => 300000,
            'funds_individuals' => 100000,
            'funds_legal_entities' => 0,
            'funds_republic' => 600000,
            'population_size' => 500,
            'population_size_settlement' => 5000, // > 4000
            'population_size_in_congregation' => 200, // 150 < 200 ≤ 300
        ]);

        $result = $this->getGPCalculation($model);

        // significantPercentage = (300000/1000000*100)-20 = 10.0 → 60*0.2=12
        // levelCoFinancing = 100000/1000000*100 = 10.0 → 80*0.3=24
        // specificShare = 500/5000*100 = 10% → 60*0.05=3
        // congregation = 200 (> 150 and ≤ 300) → 70*0.10=7
        // binary: all 0
        // Total = 12 + 24 + 3 + 7 = 46
        $this->assertEquals(46, $result);
    }

    // ======================================================================
    // GO (City District) calculation tests
    // ======================================================================

    public function test_go_returns_zero_when_project_cost_is_zero(): void
    {
        $model = $this->makeFullModel(MunicipalityTypeEnum::GO->value);
        $this->assertEquals(0, $this->getGOCalculation($model));
    }

    public function test_go_different_cofunding_brackets(): void
    {
        // GO has different brackets for individuals+legal: starts at 5% (not 1%)
        $model = $this->makeFullModel(MunicipalityTypeEnum::GO->value, [
            'funds_municipal' => 400000,   // 40% → excess 20pp → 100*0.2=20
            'funds_individuals' => 130000, // 13% → 80*0.3=24
            'funds_legal_entities' => 0,
            'funds_republic' => 470000,
            'population_size' => 200,
            'population_size_settlement' => 5000, // > 4000
            'population_size_in_congregation' => 400, // > 300
        ]);

        $result = $this->getGOCalculation($model);

        // significantPercentage = (400000/1000000*100)-20 = 20.0 → 100*0.2=20
        // levelCoFinancing = 130000/1000000*100 = 13.0 → >=12.1 ≤15.0 → 80*0.3=24
        // specificShare = 200/5000*100 = 4% → 40*0.05=2
        // congregation = 400 (>300) → 100*0.10=10
        // binary: all 0
        // Total = 20 + 24 + 2 + 10 = 56
        $this->assertEquals(56, $result);
    }

    public function test_go_cofunding_below_5_percent_is_zero(): void
    {
        // GO requires at least 5% from individuals+legal
        $model = $this->makeFullModel(MunicipalityTypeEnum::GO->value, [
            'funds_municipal' => 400000,
            'funds_individuals' => 40000,  // 4% < 5% → 0
            'funds_legal_entities' => 0,
            'funds_republic' => 560000,
            'population_size' => 200,
            'population_size_settlement' => 5000,
            'population_size_in_congregation' => 400,
        ]);

        $result = $this->getGOCalculation($model);

        // significantPercentage = (400000/1000000*100)-20 = 20 → 100*0.2=20
        // levelCoFinancing = 40000/1000000*100 = 4.0 < 5.0 → 0
        // specificShare = 200/5000*100 = 4% → 40*0.05=2
        // congregation = 400 (>300) → 100*0.10=10
        // Total = 20 + 0 + 2 + 10 = 32
        $this->assertEquals(32, $result);
    }

    // ======================================================================
    // SP (Rural Settlement) calculation tests
    // ======================================================================

    public function test_sp_returns_zero_when_project_cost_is_zero(): void
    {
        $model = $this->makeFullModel(MunicipalityTypeEnum::SP->value);
        $this->assertEquals(0, $this->getSPCalculation($model));
    }

    public function test_sp_municipal_base_is_10_percent(): void
    {
        // SP uses 10% base (same as MO)
        $model = $this->makeFullModel(MunicipalityTypeEnum::SP->value, [
            'funds_municipal' => 200000,   // 20% → 10pp excess → 60*0.2=12
            'funds_individuals' => 30000,  // 3% (settlement ≤ 1000 → max=2%) → 60*0.3=18
            'funds_legal_entities' => 0,
            'funds_republic' => 770000,
            'population_size' => 400,
            'population_size_settlement' => 800,
            'population_size_in_congregation' => 50,
        ]);

        $result = $this->getSPCalculation($model);

        // significantPercentage = (200000/1000000*100)-10 = 10.0 → 60*0.2=12
        // levelCoFinancing = 30000/1000000*100 = 3.0, max=2.0 (settlement≤1000) → 60*0.3=18
        // specificShare = 400/800*100 = 50% → 80*0.05=4
        // congregation = 50/800*100 = 6.25% (settlement < 4000, > 4.0 ≤ 8.0) → 75*0.10=7.5
        // binary: all 0
        // Total = 12 + 18 + 4 + 7.5 = 41.5
        $this->assertEquals(41.5, $result);
    }

    public function test_sp_large_settlement_uses_higher_cofunding_max(): void
    {
        // SP with > 1000 population → max=5% for co-funding threshold
        $model = $this->makeFullModel(MunicipalityTypeEnum::SP->value, [
            'funds_municipal' => 200000,
            'funds_individuals' => 30000,  // 3% < max(5%) → 0
            'funds_legal_entities' => 0,
            'funds_republic' => 770000,
            'population_size' => 400,
            'population_size_settlement' => 2000, // > 1000
            'population_size_in_congregation' => 50,
        ]);

        $result = $this->getSPCalculation($model);

        // significantPercentage = (200000/1000000*100)-10 = 10.0 → 60*0.2=12
        // levelCoFinancing = 3.0 < max(5.0) → 0
        // specificShare = 400/2000*100 = 20% → 60*0.05=3
        // congregation = 50/2000*100 = 2.5% (settlement < 4000, > 2.0 ≤ 4.0) → 50*0.10=5
        // binary: all 0
        // Total = 12 + 0 + 3 + 5 = 20
        $this->assertEquals(20, $result);
    }

    // ======================================================================
    // getCalculation routing tests
    // ======================================================================

    public function test_get_calculation_routes_to_mo(): void
    {
        $model = $this->makeFullModel(MunicipalityTypeEnum::MO->value, [
            'funds_municipal' => 100000,
            'funds_individuals' => 10000,
            'funds_legal_entities' => 0,
            'funds_republic' => 890000,
            'population_size' => 100,
            'population_size_settlement' => 1000,
            'population_size_in_congregation' => 50,
        ]);

        $resultDirect = $this->getMOCalculation($model);
        $resultRouted = $this->getCalculation($model);

        $this->assertEquals($resultDirect, $resultRouted);
    }

    public function test_get_calculation_returns_zero_for_unknown_type(): void
    {
        $municipality = $this->createMock(Municipality::class);
        $municipality->method('__get')->willReturnCallback(function ($name) {
            return match ($name) {
                'type' => 'unknown_type',
                'name' => 'Test',
                default => null,
            };
        });

        $user = $this->createMock(User::class);
        $user->method('__get')->willReturnCallback(function ($name) use ($municipality) {
            return match ($name) {
                'municipality' => $municipality,
                default => null,
            };
        });

        $model = $this->createMock(PPMIApplication::class);
        $model->method('__get')->willReturnCallback(function ($name) use ($user) {
            return match ($name) {
                'user' => $user,
                default => null,
            };
        });

        $this->assertEquals(0, $this->getCalculation($model));
    }

    // ======================================================================
    // Boundary condition tests
    // ======================================================================

    public function test_significant_percentage_boundary_at_zero_point_one(): void
    {
        // MO: significantPercentage < 0.1 → 0
        // funds_municipal/projectCost*100 - 10 should be < 0.1
        $model = $this->makeFullModel(MunicipalityTypeEnum::MO->value, [
            'funds_municipal' => 100500,  // 10.05% → excess = 0.05 < 0.1
            'funds_individuals' => 0,
            'funds_legal_entities' => 0,
            'funds_republic' => 899500,
            'population_size' => 100,
            'population_size_settlement' => 1000,
            'population_size_in_congregation' => 50,
        ]);

        $result = $this->getMOCalculation($model);

        // significantPercentage = 10.05 - 10 = 0.05 → rounds to 0.1 in number_format
        // Actually: number_format(0.05, 1) = "0.1" → but condition is < 0.1...
        // 0.1 is NOT < 0.1, so it goes to next bracket: > 0.0 && <= 5.0 → 40*0.2=8
        // Let me verify: (100500/1000000*100) - 10 = 0.05, number_format(0.05, 1) = "0.1"
        // 0.1 < 0.1 is false → next: 0.1 > 0.0 → true, 0.1 <= 5.0 → true → 40*0.2=8

        // cofunding: 0/1000000*100 = 0% → 0
        // specificShare = 10% → 60*0.05=3
        // congregation = 5% → 75*0.10=7.5
        // Total = 8 + 0 + 3 + 7.5 = 18.5
        $this->assertEquals(18.5, $result);
    }

    public function test_cofunding_boundary_between_brackets(): void
    {
        // Test the 1.99 → 2.0 boundary for co-financing
        $model1 = $this->makeFullModel(MunicipalityTypeEnum::MO->value, [
            'funds_municipal' => 260000,
            'funds_individuals' => 19900, // 1.99% → 50*0.3=15
            'funds_legal_entities' => 0,
            'funds_republic' => 720100,
            'population_size' => 100,
            'population_size_settlement' => 1000,
            'population_size_in_congregation' => 50,
        ]);

        $model2 = $this->makeFullModel(MunicipalityTypeEnum::MO->value, [
            'funds_municipal' => 260000,
            'funds_individuals' => 20000, // 2.0% → 60*0.3=18
            'funds_legal_entities' => 0,
            'funds_republic' => 720000,
            'population_size' => 100,
            'population_size_settlement' => 1000,
            'population_size_in_congregation' => 50,
        ]);

        $result1 = $this->getMOCalculation($model1);
        $result2 = $this->getMOCalculation($model2);

        // The co-financing bracket should change between 1.99% and 2.0%
        $this->assertNotEquals($result1, $result2);
    }

    // ======================================================================
    // Helper method tests
    // ======================================================================

    public function test_individuals_and_legal_entities_funds(): void
    {
        $model = $this->makePPMIModel([
            'funds_individuals' => 50000,
            'funds_legal_entities' => 30000,
        ]);

        $this->assertEquals(80000, $this->getIndividualsAndLegalEntitiesFunds($model));
    }

    public function test_is_district_plus_gp_when_municipality_has_flag(): void
    {
        $model = $this->makeFullModel(MunicipalityTypeEnum::MR->value, [], [], true);
        $this->assertTrue($this->isDistrictPlusGp($model));
    }

    public function test_is_district_plus_gp_when_no_flag(): void
    {
        $model = $this->makeFullModel(MunicipalityTypeEnum::MR->value, [], [], false);
        $this->assertFalse($this->isDistrictPlusGp($model));
    }
}
