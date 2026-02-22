<?php

namespace Tests\Unit\Calculation;

use App\Models\Register;
use App\Models\SZPTOSApplication;
use App\Traits\CalculationSZPTOSApplicationTrait;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for SZPTOS application scoring.
 *
 * Tests all scoring criteria with boundary conditions to ensure
 * calculations remain correct during migration.
 */
class CalculationSZPTOSTest extends TestCase
{
    use CalculationSZPTOSApplicationTrait;

    /**
     * Create a mock SZPTOSApplication with specified attributes and relations.
     */
    private function makeModel(array $attrs = [], array $relations = []): SZPTOSApplication
    {
        $model = $this->createMock(SZPTOSApplication::class);

        $defaults = [
            'total_cost_project' => 0,
            'extra_budgetary_sources' => 0,
            'population_size_settlement' => 0,
            'number_beneficiaries' => 0,
            'number_present_at_general_meeting' => 0,
            'is_grand_opening_with_media_coverage' => false,
            'funds_local_budget' => 0,
        ];

        $defaultRelations = [
            'participation_population_in_implementation_project' => new Collection(),
            'public_participation_in_operation_facility' => new Collection(),
            'project_implementation_provides_informational_support' => new Collection(),
            'register' => null,
        ];

        $merged = array_merge($defaults, $attrs);
        $mergedRelations = array_merge($defaultRelations, $relations);

        $model->method('__get')->willReturnCallback(function ($name) use ($merged, $mergedRelations) {
            if (isset($mergedRelations[$name])) {
                return $mergedRelations[$name];
            }
            return $merged[$name] ?? null;
        });

        return $model;
    }

    private function makeRegister(int $numberCitizens): Register
    {
        $register = $this->createMock(Register::class);
        $register->method('__get')->willReturnCallback(function ($name) use ($numberCitizens) {
            return match ($name) {
                'number_citizens' => $numberCitizens,
                default => null,
            };
        });
        return $register;
    }

    // ======================================================================
    // projectEconomicEfficiency tests
    // ======================================================================

    public function test_economic_efficiency_zero_when_no_cost(): void
    {
        $model = $this->makeModel(['total_cost_project' => 0]);
        $this->assertEquals(0, $this->projectEconomicEfficiency($model));
    }

    public function test_economic_efficiency_bracket_up_to_10_percent(): void
    {
        $model = $this->makeModel([
            'total_cost_project' => 100000,
            'extra_budgetary_sources' => 8000, // 8%
        ]);
        $this->assertEquals(1, $this->projectEconomicEfficiency($model));
    }

    public function test_economic_efficiency_bracket_11_to_18_percent(): void
    {
        $model = $this->makeModel([
            'total_cost_project' => 100000,
            'extra_budgetary_sources' => 15000, // 15%
        ]);
        $this->assertEquals(2, $this->projectEconomicEfficiency($model));
    }

    public function test_economic_efficiency_bracket_19_to_24_percent(): void
    {
        $model = $this->makeModel([
            'total_cost_project' => 100000,
            'extra_budgetary_sources' => 20000, // 20%
        ]);
        $this->assertEquals(3, $this->projectEconomicEfficiency($model));
    }

    public function test_economic_efficiency_bracket_25_to_30_percent(): void
    {
        $model = $this->makeModel([
            'total_cost_project' => 100000,
            'extra_budgetary_sources' => 27000, // 27%
        ]);
        $this->assertEquals(4, $this->projectEconomicEfficiency($model));
    }

    public function test_economic_efficiency_bracket_above_30_percent(): void
    {
        $model = $this->makeModel([
            'total_cost_project' => 100000,
            'extra_budgetary_sources' => 35000, // 35%
        ]);
        $this->assertEquals(5, $this->projectEconomicEfficiency($model));
    }

    // ======================================================================
    // projectSocialEffectiveness tests (population ≤ 3000)
    // ======================================================================

    public function test_social_effectiveness_small_settlement_zero_population(): void
    {
        $model = $this->makeModel(['population_size_settlement' => 0]);
        $this->assertEquals(0, $this->projectSocialEffectiveness1($model));
    }

    public function test_social_effectiveness_small_settlement_up_to_2_percent(): void
    {
        $model = $this->makeModel([
            'population_size_settlement' => 1000,
            'number_beneficiaries' => 15, // 1.5%
        ]);
        $this->assertEquals(1, $this->projectSocialEffectiveness1($model));
    }

    public function test_social_effectiveness_small_settlement_above_15_percent(): void
    {
        $model = $this->makeModel([
            'population_size_settlement' => 1000,
            'number_beneficiaries' => 200, // 20%
        ]);
        $this->assertEquals(5, $this->projectSocialEffectiveness1($model));
    }

    public function test_social_effectiveness_ignores_large_settlement(): void
    {
        $model = $this->makeModel([
            'population_size_settlement' => 5000, // > 3000
            'number_beneficiaries' => 200,
        ]);
        $this->assertEquals(0, $this->projectSocialEffectiveness1($model));
    }

    // ======================================================================
    // projectSocialEffectiveness2 tests (population > 3000)
    // ======================================================================

    public function test_social_effectiveness_large_settlement_up_to_50(): void
    {
        $model = $this->makeModel([
            'population_size_settlement' => 5000,
            'number_beneficiaries' => 30,
        ]);
        $this->assertEquals(1, $this->projectSocialEffectiveness2($model));
    }

    public function test_social_effectiveness_large_settlement_above_200(): void
    {
        $model = $this->makeModel([
            'population_size_settlement' => 5000,
            'number_beneficiaries' => 250,
        ]);
        $this->assertEquals(5, $this->projectSocialEffectiveness2($model));
    }

    public function test_social_effectiveness_large_ignores_small_settlement(): void
    {
        $model = $this->makeModel([
            'population_size_settlement' => 2000, // ≤ 3000
            'number_beneficiaries' => 250,
        ]);
        $this->assertEquals(0, $this->projectSocialEffectiveness2($model));
    }

    // ======================================================================
    // participationPopulationInImplementationProject tests
    // ======================================================================

    public function test_participation_empty_is_zero(): void
    {
        $model = $this->makeModel();
        $this->assertEquals(0, $this->participationPopulationInImplementationProject($model));
    }

    public function test_participation_counts_up_to_5(): void
    {
        $items = new Collection([
            (object)['field68' => 'Activity 1'],
            (object)['field68' => 'Activity 2'],
            (object)['field68' => 'Activity 3'],
        ]);

        $model = $this->makeModel([], [
            'participation_population_in_implementation_project' => $items,
        ]);

        $this->assertEquals(3, $this->participationPopulationInImplementationProject($model));
    }

    public function test_participation_capped_at_5(): void
    {
        $items = new Collection([
            (object)['field68' => 'Act 1'],
            (object)['field68' => 'Act 2'],
            (object)['field68' => 'Act 3'],
            (object)['field68' => 'Act 4'],
            (object)['field68' => 'Act 5'],
            (object)['field68' => 'Act 6'],
            (object)['field68' => 'Act 7'],
        ]);

        $model = $this->makeModel([], [
            'participation_population_in_implementation_project' => $items,
        ]);

        $this->assertEquals(5, $this->participationPopulationInImplementationProject($model));
    }

    public function test_participation_skips_empty_field68(): void
    {
        $items = new Collection([
            (object)['field68' => 'Activity'],
            (object)['field68' => ''],
            (object)['field68' => null],
        ]);

        $model = $this->makeModel([], [
            'participation_population_in_implementation_project' => $items,
        ]);

        $this->assertEquals(1, $this->participationPopulationInImplementationProject($model));
    }

    // ======================================================================
    // degreePublicParticipationInDefinitionProblem tests
    // ======================================================================

    public function test_degree_participation_zero_without_register(): void
    {
        $model = $this->makeModel();
        $this->assertEquals(0, $this->degreePublicParticipationInDefinitionProblem($model));
    }

    public function test_degree_participation_bracket_up_to_30_percent(): void
    {
        $model = $this->makeModel([
            'number_present_at_general_meeting' => 20,
        ], [
            'register' => $this->makeRegister(100), // 20%
        ]);
        $this->assertEquals(1, $this->degreePublicParticipationInDefinitionProblem($model));
    }

    public function test_degree_participation_bracket_31_to_60_percent(): void
    {
        $model = $this->makeModel([
            'number_present_at_general_meeting' => 50,
        ], [
            'register' => $this->makeRegister(100), // 50%
        ]);
        $this->assertEquals(3, $this->degreePublicParticipationInDefinitionProblem($model));
    }

    public function test_degree_participation_bracket_above_60_percent(): void
    {
        $model = $this->makeModel([
            'number_present_at_general_meeting' => 80,
        ], [
            'register' => $this->makeRegister(100), // 80%
        ]);
        $this->assertEquals(5, $this->degreePublicParticipationInDefinitionProblem($model));
    }

    // ======================================================================
    // grandOpeningWithMediaCoverage tests
    // ======================================================================

    public function test_grand_opening_no(): void
    {
        $model = $this->makeModel(['is_grand_opening_with_media_coverage' => false]);
        $this->assertEquals(0, $this->grandOpeningWithMediaCoverage($model));
    }

    public function test_grand_opening_yes(): void
    {
        $model = $this->makeModel(['is_grand_opening_with_media_coverage' => true]);
        $this->assertEquals(1, $this->grandOpeningWithMediaCoverage($model));
    }

    // ======================================================================
    // coFinancingProjectLocalBudget tests
    // ======================================================================

    public function test_cofunding_zero_when_no_cost(): void
    {
        $model = $this->makeModel(['total_cost_project' => 0]);
        $this->assertEquals(0, $this->coFinancingProjectLocalBudget($model));
    }

    public function test_cofunding_zero_when_below_1_percent(): void
    {
        $model = $this->makeModel([
            'total_cost_project' => 100000,
            'funds_local_budget' => 500, // 0.5%
        ]);
        $this->assertEquals(0, $this->coFinancingProjectLocalBudget($model));
    }

    public function test_cofunding_3_when_at_or_above_1_percent(): void
    {
        $model = $this->makeModel([
            'total_cost_project' => 100000,
            'funds_local_budget' => 1500, // 1.5%
        ]);
        $this->assertEquals(3, $this->coFinancingProjectLocalBudget($model));
    }

    // ======================================================================
    // numbersInhabitants tests
    // ======================================================================

    public function test_numbers_inhabitants_zero_when_above_1000(): void
    {
        $model = $this->makeModel(['population_size_settlement' => 1500]);
        $this->assertEquals(0, $this->numbersInhabitants($model));
    }

    public function test_numbers_inhabitants_1_when_below_1000(): void
    {
        $model = $this->makeModel(['population_size_settlement' => 800]);
        $this->assertEquals(1, $this->numbersInhabitants($model));
    }

    public function test_numbers_inhabitants_zero_at_exactly_1000(): void
    {
        $model = $this->makeModel(['population_size_settlement' => 1000]);
        $this->assertEquals(0, $this->numbersInhabitants($model));
    }

    // ======================================================================
    // Full calculation test
    // ======================================================================

    public function test_full_calculation_sums_all_criteria(): void
    {
        $items3 = new Collection([
            (object)['field68' => 'Activity 1'],
            (object)['field68' => 'Activity 2'],
        ]);
        $items2 = new Collection([
            (object)['field69' => 'Maintenance 1'],
        ]);
        $items1 = new Collection([
            (object)['field70' => 'Info 1'],
            (object)['field70' => 'Info 2'],
            (object)['field70' => 'Info 3'],
        ]);

        $model = $this->makeModel([
            'total_cost_project' => 100000,
            'extra_budgetary_sources' => 25000,  // 25% → 4
            'population_size_settlement' => 800,  // < 1000 → +1, ≤ 3000 → social1
            'number_beneficiaries' => 80,          // 10% → 3
            'number_present_at_general_meeting' => 70,
            'is_grand_opening_with_media_coverage' => true, // → 1
            'funds_local_budget' => 2000,          // 2% → 3
        ], [
            'participation_population_in_implementation_project' => $items3, // 2
            'public_participation_in_operation_facility' => $items2,          // 1
            'project_implementation_provides_informational_support' => $items1, // 3
            'register' => $this->makeRegister(100), // 70% → 5
        ]);

        $result = $this->getCalculation($model);

        // Economic efficiency: 25% → 4
        // Social effectiveness 1 (≤3000): 80/800*100=10% → 3
        // Social effectiveness 2 (>3000): 800 ≤ 3000 → 0
        // Participation: 2 items → 2
        // Degree participation: 70/100=70% → 5
        // Grand opening: yes → 1
        // Operation maintenance: 1 item → 1
        // Informational support: 3 items → 3
        // Co-financing local budget: 2% → 3
        // Numbers inhabitants: 800 < 1000 → 1
        // Unique: always 0

        // Total = 4 + 3 + 0 + 2 + 5 + 1 + 1 + 3 + 3 + 1 + 0 = 23
        $this->assertEquals(23, $result);
    }

    public function test_minimum_calculation_all_zeros(): void
    {
        $model = $this->makeModel();
        $this->assertEquals(0, $this->getCalculation($model));
    }
}
