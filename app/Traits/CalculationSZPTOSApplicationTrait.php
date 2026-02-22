<?php

namespace App\Traits;

use App\Models\SZPTOSApplication;

trait CalculationSZPTOSApplicationTrait
{
    /**
     * -------------------------------------------
     * Получаем расчет в зависимости от поселения
     * -------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function getCalculation(SZPTOSApplication $application, $result = 0): int
    {
        $result += $this->projectEconomicEfficiency($application);
//        dump('Экономическая эффективность проекта - ' . $this->projectEconomicEfficiency($application));

        $result += $this->projectSocialEffectiveness1($application);
//        dump('Социальная эффективность проекта для населенных пунктов с численностью населения до 3000 чел. - ' . $this->projectSocialEffectiveness1($application));

        $result += $this->projectSocialEffectiveness2($application);
//        dump('Социальная эффективность проекта для населенных пунктов с численностью населения от 3001 чел. - ' . $this->projectSocialEffectiveness2($application));

        $result += $this->participationPopulationInImplementationProject($application);
//        dump('Участие населения (членов ТОС) в реализации проекта (неоплачиваемый труд, материалы и др.) - ' . $this->participationPopulationInImplementationProject($application));

        $result += $this->degreePublicParticipationInDefinitionProblem($application);
//        dump('Степень участия населения в определении проблемы, заявленной в проекте (процентное соотношение количества присутствующих на общем собрании членов ТОС к количеству зарегистрированных граждан в ТОС) - ' . $this->degreePublicParticipationInDefinitionProblem($application));

        $result += $this->grandOpeningWithMediaCoverage($application);
//        dump('По итогам реализации проекта предусмотрено мероприятие «Торжественное открытие с освещением в СМИ» (СМИ: Интернет (социальные сети), периодические издания) - ' . $this->grandOpeningWithMediaCoverage($application));

        $result += $this->participationPopulationInEnsuringOperationAndMaintenanceFacility($application);
//        dump('Участие населения в обеспечении эксплуатации и содержании объекта, после завершения проекта - ' . $this->participationPopulationInEnsuringOperationAndMaintenanceFacility($application));

        $result += $this->implementationProjectProvidesForInformationalSupport($application);
//        dump('Реализацией проекта предусмотрено его информационное сопровождение - ' . $this->implementationProjectProvidesForInformationalSupport($application));

        $result += $this->coFinancingProjectLocalBudget($application);
//        dump('Предусмотрено софинансирование проекта из местного бюджета в размере не менее 1% от общего объема расходов на реализацию проекта - ' . $this->coFinancingProjectLocalBudget($application));

        $result += $this->numbersInhabitants($application);
//        dump('Численность жителей поселения менее 1000 чел. - ' . $this->numbersInhabitants($application));

        $result += $this->projectIsUnique($application);
//        dump('Проект является уникальным, важным и отличается от других проектов - ' . $this->projectIsUnique($application));

//        dd($result);

        return $result;
    }

    /**
     * ------------------------------------
     * Экономическая эффективность проекта
     * ------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function projectEconomicEfficiency(SZPTOSApplication $application, $result = 0): int
    {
        if (!!$application->total_cost_project) {

            $percent = (float)number_format((($application->extra_budgetary_sources / $application->total_cost_project) * 100), 1, '.', '');

            if ($percent <= 10.0) {

                $result += 1;
            } elseif ($percent > 10.0 && $percent <= 18.0) {

                $result += 2;
            } elseif ($percent > 18.0 && $percent <= 24.0) {

                $result += 3;
            } elseif ($percent > 24.0 && $percent <= 30.0) {

                $result += 4;
            } else {

                $result += 5;
            }
        }
        return $result;
    }

    /**
     * ----------------------------------------------------------------------------------------------
     * Социальная эффективность проекта для населенных пунктов с численностью населения до 3000 чел.
     * ----------------------------------------------------------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function projectSocialEffectiveness1(SZPTOSApplication $application, $result = 0): int
    {
        if (!!$application->population_size_settlement && $application->population_size_settlement <= 3000) {

            $percent = (float) number_format((((int) $application->number_beneficiaries / $application->population_size_settlement) * 100), 1, '.', '');

            if ($percent <= 2.0) {

                $result += 1;
            } elseif ($percent > 2.0 && $percent <= 6.0) {

                $result += 2;
            } elseif ($percent > 6.0 && $percent <= 10.0) {

                $result += 3;
            } elseif ($percent > 10.0 && $percent <= 15.0) {

                $result += 4;
            } else {

                $result += 5;
            }
        }

        return $result;
    }

    /**
     * ----------------------------------------------------------------------------------------------
     * Социальная эффективность проекта для населенных пунктов с численностью населения от 3001 чел.
     * ----------------------------------------------------------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function projectSocialEffectiveness2(SZPTOSApplication $application, $result = 0): int
    {
        if (!!$application->population_size_settlement && $application->population_size_settlement > 3000) {

            $countHumans = $application->number_beneficiaries;

            if ($countHumans <= 50.0) {

                $result += 1;
            } elseif ($countHumans > 50.0 && $countHumans <= 100.0) {

                $result += 2;
            } elseif ($countHumans > 100.0 && $countHumans <= 150.0) {

                $result += 3;
            } elseif ($countHumans > 150.0 && $countHumans <= 200.0) {

                $result += 4;
            } else {

                $result += 5;
            }
        }

        return $result;
    }

    /**
     * -------------------------------------------------------------------------------------------
     * Участие населения (членов ТОС) в реализации проекта (неоплачиваемый труд, материалы и др.)
     * -------------------------------------------------------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function participationPopulationInImplementationProject(SZPTOSApplication $application, $result = 0): int
    {
        $filtering = $application->participation_population_in_implementation_project->filter(function ($query) {

            if(!empty($query->field68)) {

                return $query;
            }
        });

        foreach ($filtering as $key => $item) {

            if ($key > 4) {

                continue;
            }

            $result += 1;
        }

        return $result;
    }

    /**
     * --------------------------------------------------------------------
     * Степень участия населения в определении проблемы, заявленной в
     * проекте (процентное соотношение количества присутствующих на общем
     * собрании членов ТОС к количеству зарегистрированных граждан в ТОС)
     * --------------------------------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function degreePublicParticipationInDefinitionProblem(SZPTOSApplication $application, $result = 0): int
    {
        if (!!$application->register?->number_citizens) {

            $percent = (float) number_format((((int) $application->number_present_at_general_meeting / $application->register?->number_citizens) * 100), 1, '.', '');

            if ($percent <= 30.0) {

                $result += 1;
            } elseif ($percent > 30.0 && $percent <= 60.0) {

                $result += 3;
            } else {

                $result += 5;
            }
        }

        return $result;
    }

    /**
     * -------------------------------------------------------
     * По итогам реализации проекта предусмотрено мероприятие
     * «Торжественное открытие с освещением в СМИ» (СМИ:
     * Интернет (социальные сети), периодические издания)
     * -------------------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function grandOpeningWithMediaCoverage(SZPTOSApplication $application, $result = 0): int
    {
        if (!!$application->is_grand_opening_with_media_coverage) {

            $result += 1;
        }

        return $result;
    }

    /**
     * --------------------------------------------------------------------------------------------
     * Участие населения в обеспечении эксплуатации и содержании объекта, после завершения проекта
     * --------------------------------------------------------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function participationPopulationInEnsuringOperationAndMaintenanceFacility(SZPTOSApplication $application, $result = 0): int
    {
        $filtering = $application->public_participation_in_operation_facility->filter(function ($query) {

            if(!empty($query->field69)) {

                return $query;
            }
        });

        foreach ($filtering as $key => $item) {

            if ($key > 2) {

                continue;
            }

            $result += 1;
        }

        return $result;
    }

    /**
     * -------------------------------------------------------------------
     * Реализацией проекта предусмотрено его информационное сопровождение
     * -------------------------------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function implementationProjectProvidesForInformationalSupport(SZPTOSApplication $application, $result = 0): int
    {
        $filtering = $application->project_implementation_provides_informational_support->filter(function ($query) {

            if(!empty($query->field70)) {

                return $query;
            }
        });


        foreach ($filtering as $key => $item) {

            if ($key > 4) {

                continue;
            }

            $result += 1;
        }

        return $result;
    }

    /**
     * --------------------------------------------------------------------
     * Предусмотрено софинансирование проекта из местного бюджета в
     * размере не менее 1% от общего объема расходов на реализацию проекта
     * --------------------------------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function coFinancingProjectLocalBudget(SZPTOSApplication $application, $result = 0): int
    {
        if (!!$application->total_cost_project) {

            $percent = (float) number_format((((int) $application->funds_local_budget / $application->total_cost_project) * 100), 1, '.', '');

            if ($percent >= 1.0) {

                $result += 3;
            }
        }

        return $result;
    }

    /**
     * ----------------------------------------------
     * Численность жителей поселения менее 1000 чел.
     * ----------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function numbersInhabitants(SZPTOSApplication $application, $result = 0): int
    {
        if (!!$application->population_size_settlement && $application->population_size_settlement < 1000) {

            $result += 1;
        }

        return $result;
    }

    /**
     * -------------------------------------------------------------------
     * Проект является уникальным, важным и отличается от других проектов
     * -------------------------------------------------------------------
     *
     * @param SZPTOSApplication $application
     * @param int $result
     * @return int
     */
    public function projectIsUnique(SZPTOSApplication $application, $result = 0): int
    {

        return $result;
    }
}
