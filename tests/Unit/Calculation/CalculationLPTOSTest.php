<?php

namespace Tests\Unit\Calculation;

use App\Models\Import\ApplicationTOS;
use App\Traits\CalculationLPTOSApplicationTrait;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for LPTOS application scoring.
 *
 * Each criterion is a simple multiplier of an admin-set score (1-5).
 * Tests verify the weights are correct.
 */
class CalculationLPTOSTest extends TestCase
{
    use CalculationLPTOSApplicationTrait;

    private function makeModel(array $attrs = []): ApplicationTOS
    {
        $model = $this->createMock(ApplicationTOS::class);

        $defaults = [
            'calc_pri' => 0,
            'calc_npbp' => 0,
            'calc_nip' => 0,
            'calc_varp' => 0,
            'calc_papiwaf' => 0,
            'calc_wsdop' => 0,
            'calc_epfpi' => 0,
            'calc_epfpb' => 0,
            'calc_vaebf' => 0,
            'calc_uvm' => 0,
            'calc_uspm' => 0,
            'calc_nmh' => 0,
            'calc_ciaaaatosim' => 0,
        ];

        $merged = array_merge($defaults, $attrs);

        $model->method('__get')->willReturnCallback(function ($name) use ($merged) {
            return $merged[$name] ?? null;
        });

        return $model;
    }

    public function test_empty_scores_zero(): void
    {
        $model = $this->makeModel();
        $this->assertEquals(0, $this->getCalculation($model));
    }

    public function test_percentage_residents_involvement_weight_5(): void
    {
        $model = $this->makeModel(['calc_pri' => 3]);
        $this->assertEquals(15, $this->percentageResidentsInvolvement($model));
    }

    public function test_number_beneficiaries_weight_4(): void
    {
        $model = $this->makeModel(['calc_npbp' => 2]);
        $this->assertEquals(8, $this->numberPeopleBeneficiariesProject($model));
    }

    public function test_number_implemented_practices_weight_3(): void
    {
        $model = $this->makeModel(['calc_nip' => 4]);
        $this->assertEquals(12, $this->numberImplementedPractices($model));
    }

    public function test_validity_and_relevance_weight_2(): void
    {
        $model = $this->makeModel(['calc_varp' => 5]);
        $this->assertEquals(10, $this->validityAndRelevanceProblem($model));
    }

    public function test_prospect_additional_implementation_weight_3(): void
    {
        $model = $this->makeModel(['calc_papiwaf' => 1]);
        $this->assertEquals(3, $this->prospectAdditionalProjectImplementationWithoutAdditionalFunding($model));
    }

    public function test_work_scale_weight_3(): void
    {
        $model = $this->makeModel(['calc_wsdop' => 2]);
        $this->assertEquals(6, $this->workScaleDoneOnProject($model));
    }

    public function test_efficiency_per_inhabitant_weight_1(): void
    {
        $model = $this->makeModel(['calc_epfpi' => 5]);
        $this->assertEquals(5, $this->efficiencyProjectFinancialPerInhabitant($model));
    }

    public function test_efficiency_per_beneficiary_weight_5(): void
    {
        $model = $this->makeModel(['calc_epfpb' => 3]);
        $this->assertEquals(15, $this->efficiencyProjectFinancialPerBeneficiary($model));
    }

    public function test_extra_budgetary_financing_weight_5(): void
    {
        $model = $this->makeModel(['calc_vaebf' => 4]);
        $this->assertEquals(20, $this->volumeAttractedExtraBudgetaryFinancing($model));
    }

    public function test_volunteering_weight_2(): void
    {
        $model = $this->makeModel(['calc_uvm' => 3]);
        $this->assertEquals(6, $this->usingVolunteeringMechanisms($model));
    }

    public function test_social_partnership_weight_4(): void
    {
        $model = $this->makeModel(['calc_uspm' => 2]);
        $this->assertEquals(8, $this->usingSocialPartnershipMechanisms($model));
    }

    public function test_meetings_weight_2(): void
    {
        $model = $this->makeModel(['calc_nmh' => 5]);
        $this->assertEquals(10, $this->numberMeetingsHeld($model));
    }

    public function test_media_coverage_weight_5(): void
    {
        $model = $this->makeModel(['calc_ciaaaatosim' => 4]);
        $this->assertEquals(20, $this->coverageInformationAboutActivitiesAndAchievementsTOSInMedia($model));
    }

    public function test_full_calculation_with_max_scores(): void
    {
        $model = $this->makeModel([
            'calc_pri' => 5,           // 5*5 = 25
            'calc_npbp' => 5,          // 5*4 = 20
            'calc_nip' => 5,           // 5*3 = 15
            'calc_varp' => 5,          // 5*2 = 10
            'calc_papiwaf' => 5,       // 5*3 = 15
            'calc_wsdop' => 5,         // 5*3 = 15
            'calc_epfpi' => 5,         // 5*1 = 5
            'calc_epfpb' => 5,         // 5*5 = 25
            'calc_vaebf' => 5,         // 5*5 = 25
            'calc_uvm' => 5,           // 5*2 = 10
            'calc_uspm' => 5,          // 5*4 = 20
            'calc_nmh' => 5,           // 5*2 = 10
            'calc_ciaaaatosim' => 5,   // 5*5 = 25
        ]);

        // Total weights: 5+4+3+2+3+3+1+5+5+2+4+2+5 = 44
        // Max score per criterion = 5
        // Max total = 44*5 = 220
        $this->assertEquals(220, $this->getCalculation($model));
    }
}
