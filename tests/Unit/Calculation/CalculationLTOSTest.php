<?php

namespace Tests\Unit\Calculation;

use App\Models\LTOSApplication;
use App\Traits\CalculationLTOSApplicationTrait;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for LTOS application scoring.
 *
 * Tests key scoring criteria and their summation.
 */
class CalculationLTOSTest extends TestCase
{
    use CalculationLTOSApplicationTrait;

    private function makeModel(array $attrs = [], array $relations = []): LTOSApplication
    {
        $model = $this->createMock(LTOSApplication::class);

        $defaults = [
            'created_at' => '2024-01-01 00:00:00',
        ];

        $defaultRelations = [
            'organization_cultural_events' => new Collection(),
            'conducting_sports_competitions' => new Collection(),
            'drug_addiction_prevention_measures' => new Collection(),
            'availability_clubs' => new Collection(),
            'measures_organization_landscaping' => new Collection(),
            'number_objects_social_orientation' => new Collection(),
            'providing_assistance' => new Collection(),
            'healthy_lifestyle_corner' => new Collection(),
            'joint_preventive_measures' => new Collection(),
            'fire_prevention' => new Collection(),
            'meetings_and_seminars' => new Collection(),
            'placement_information_in_mass_media' => new Collection(),
            'participation_in_previous_contests_unsuccessful' => new Collection(),
            'participation_in_previous_contests_successful' => new Collection(),
            'awards' => new Collection(),
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

    public function test_empty_application_scores_zero(): void
    {
        $model = $this->makeModel();
        $this->assertEquals(0, $this->getCalculation($model));
    }

    public function test_cultural_events_sum_field22(): void
    {
        $events = new Collection([
            (object)['field22' => 3, 'field23' => 'Event 1', 'field24' => 'Desc'],
            (object)['field22' => 2, 'field23' => 'Event 2', 'field24' => 'Desc'],
        ]);

        $model = $this->makeModel([], ['organization_cultural_events' => $events]);
        $this->assertEquals(5, $this->organization_cultural_events($model));
    }

    public function test_sports_competitions_sum_field25(): void
    {
        $items = new Collection([
            (object)['field25' => 1, 'field26' => 'Sport', 'field27' => ''],
        ]);

        $model = $this->makeModel([], ['conducting_sports_competitions' => $items]);
        $this->assertEquals(1, $this->conducting_sports_competitions($model));
    }

    public function test_clubs_count_2_each(): void
    {
        $clubs = new Collection([
            (object)['field31' => 'Club A', 'field32' => 'Desc'],
            (object)['field31' => 'Club B', 'field32' => ''],
            (object)['field31' => '', 'field32' => ''], // empty → skipped
        ]);

        $model = $this->makeModel([], ['availability_clubs' => $clubs]);
        $this->assertEquals(4, $this->availability_clubs($model));
    }

    public function test_providing_assistance_is_binary_1(): void
    {
        $items = new Collection([
            (object)['field38' => 'Help 1', 'field39' => ''],
            (object)['field38' => 'Help 2', 'field39' => ''],
        ]);

        $model = $this->makeModel([], ['providing_assistance' => $items]);
        // Always 1 regardless of count
        $this->assertEquals(1, $this->providing_assistance($model));
    }

    public function test_healthy_lifestyle_is_binary_2(): void
    {
        $items = new Collection([
            (object)['field40' => 'Corner', 'field41' => ''],
        ]);

        $model = $this->makeModel([], ['healthy_lifestyle_corner' => $items]);
        $this->assertEquals(2, $this->healthy_lifestyle_corner($model));
    }

    public function test_awards_count_0_2_each(): void
    {
        $items = new Collection([
            (object)['field58' => 'Award 1', 'field59' => '2023'],
            (object)['field58' => 'Award 2', 'field59' => '2022'],
            (object)['field58' => 'Award 3', 'field59' => '2021'],
        ]);

        $model = $this->makeModel([], ['awards' => $items]);
        $this->assertEqualsWithDelta(0.6, $this->awards($model), 0.001);
    }

    public function test_previous_contests_count_1_each(): void
    {
        $unsuccessful = new Collection([
            (object)['field54' => 'Contest 1', 'field55' => '2022'],
            (object)['field54' => 'Contest 2', 'field55' => '2023'],
        ]);
        $successful = new Collection([
            (object)['field56' => 'Contest 3', 'field57' => '2023'],
        ]);

        $model = $this->makeModel([], [
            'participation_in_previous_contests_unsuccessful' => $unsuccessful,
            'participation_in_previous_contests_successful' => $successful,
        ]);

        $this->assertEquals(2, $this->participation_in_previous_contests_unsuccessful($model));
        $this->assertEquals(1, $this->participation_in_previous_contests_successful($model));
    }

    public function test_full_calculation_sums_all(): void
    {
        $model = $this->makeModel([], [
            'organization_cultural_events' => new Collection([
                (object)['field22' => 2, 'field23' => 'E', 'field24' => ''],
            ]),
            'availability_clubs' => new Collection([
                (object)['field31' => 'Club', 'field32' => ''],
            ]),
            'providing_assistance' => new Collection([
                (object)['field38' => 'Help', 'field39' => ''],
            ]),
            'awards' => new Collection([
                (object)['field58' => 'Award', 'field59' => '2023'],
            ]),
        ]);

        $result = $this->getCalculation($model);
        // cultural: 2, clubs: 2, assistance: 1, awards: 0.2
        $this->assertEqualsWithDelta(5.2, $result, 0.001);
    }
}
