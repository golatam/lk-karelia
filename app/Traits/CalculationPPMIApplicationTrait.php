<?php

namespace App\Traits;

use App\Enums\MunicipalityTypeEnum;
use App\Models\PPMIApplication;

trait CalculationPPMIApplicationTrait
{
    /**
     * Включение тестовой среды
     * @return bool
     */
    public function isTestMode(): bool
    {
        return false;
    }

    public function showDump($data, $key = null, $separator = '.'): void
    {
        if ($this->isTestMode()) {
            $result = $key ? "{$key}{$separator} {$data}" : $data;
            dump($result);
        }
    }

    public function showDD($data, $key = null, $separator = ':'): void
    {
        if ($this->isTestMode()) {
            $result = $key ? "{$key}{$separator} {$data}" : $data;
            dd($result);
        }
    }

    /**
     * -------------------------------------------
     * Получаем расчет в зависимости от поселения
     * -------------------------------------------
     *
     * @param PPMIApplication $model
     * @return float|int
     */
    public function getCalculation(PPMIApplication $model): float|int
    {
        $municipalityName = $model->user?->municipality?->name;

        $this->showDump($municipalityName, 'Муниципалитет', ':');

        return match ($model->user?->municipality?->type) {
            MunicipalityTypeEnum::MO->value => $this->getMOCalculation($model),
            MunicipalityTypeEnum::MR->value => $this->getMRCalculation($model),
            MunicipalityTypeEnum::GO->value => $this->getGOCalculation($model),
            MunicipalityTypeEnum::GP->value => $this->getGPCalculation($model),
            MunicipalityTypeEnum::SP->value => $this->getSPCalculation($model),
            default => 0
        };
    }

    /**
     * --------------------------------------
     * Получаем расчет муниципального округа
     * --------------------------------------
     *
     * @param PPMIApplication $model
     * @param int $result
     * @return float|int
     */
    public function getMOCalculation(PPMIApplication $model, $result = 0): float|int
    {
        $projectCost = $this->getProjectCost($model);

        if (!$projectCost) {

            return 0;
        }

        // превышение уровня софинансирования проекта за счет бюджета муниципального образования (в процентных пунктах)
        $significantPercentage = (float) number_format((($model->funds_municipal / $projectCost * 100) - 10), 1, '.', '');

        $ratio = 0.2;
        if ($significantPercentage < 0.1) {
            $significantPercentageResult = 0;
        } elseif ($significantPercentage > 0.0 && $significantPercentage <= 5.0) {
            $significantPercentageResult = 40 * $ratio;
        } elseif ($significantPercentage > 5.0 && $significantPercentage <= 10.0) {
            $significantPercentageResult = 60 * $ratio;
        } elseif ($significantPercentage > 10.0 && $significantPercentage <= 15.0) {
            $significantPercentageResult = 80 * $ratio;
        } else {
            $significantPercentageResult = 100 * $ratio;
        }
        $this->showDump($significantPercentageResult, 1);
        $result += $significantPercentageResult;

        $individualsAndLegalEntitiesFunds = $this->getIndividualsAndLegalEntitiesFunds($model);

        // уровень софинансирования проекта за счет средств физических и юридических лиц в денежной форме (процентов от предполагаемой стоимости проекта)
        $levelCoFinancingOfProject = (float) number_format(($individualsAndLegalEntitiesFunds / $projectCost * 100), 1, '.', '');

        $ratio = 0.3;
        if ($levelCoFinancingOfProject < 1.0) {
            $levelCoFinancingOfProjectResult = 0;
        } elseif ($levelCoFinancingOfProject >= 1.0 && $levelCoFinancingOfProject <= 1.99) {
            $levelCoFinancingOfProjectResult = 50 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 2.0 && $levelCoFinancingOfProject <= 5.0) {
            $levelCoFinancingOfProjectResult = 60 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 5.1 && $levelCoFinancingOfProject <= 7.0) {
            $levelCoFinancingOfProjectResult = 70 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 7.1 && $levelCoFinancingOfProject <= 10.0) {
            $levelCoFinancingOfProjectResult = 80 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 10.1 && $levelCoFinancingOfProject <= 14.0) {
            $levelCoFinancingOfProjectResult = 90 * $ratio;
        } else {
            $levelCoFinancingOfProjectResult = 100 * $ratio;
        }
        $this->showDump($levelCoFinancingOfProjectResult, 2);
        $result += $levelCoFinancingOfProjectResult;

        // удельный вес (доля) населения, которое будет регулярно пользоваться результатами от реализации проекта
        $specificSharePopulation = (float) number_format((($model->population_size / $model->population_size_settlement * 100)), 1, '.', '');

        $ratio = 0.05;
        if ($specificSharePopulation <= 5.0) {
            $specificSharePopulationResult = 40 * $ratio;
        } elseif ($specificSharePopulation > 5.0 && $specificSharePopulation <= 30.0) {
            $specificSharePopulationResult = 60 * $ratio;
        } elseif ($specificSharePopulation > 30.0 && $specificSharePopulation <= 50.0) {
            $specificSharePopulationResult = 80 * $ratio;
        } else {
            $specificSharePopulationResult = 100 * $ratio;
        }
        $this->showDump($specificSharePopulationResult, 3);
        $result += $specificSharePopulationResult;

        // участие населения в определении проблемы и выборе проекта согласно протоколу собрания граждан

        $ratio = 0.10;
        $specificSharePopulation = (float) number_format((($model->population_size_in_congregation / $model->population_size_settlement * 100)), 1, '.', '');
        $numberFormat = (float) number_format(($model->population_size_in_congregation), 1, '.', '');
        if ($model->population_size_settlement > 4000) {
            if ($numberFormat <= 50.0) {
                $specificSharePopulationResult = 25 * $ratio;
            } elseif ($numberFormat > 50.0 && $numberFormat <= 150.0) {
                $specificSharePopulationResult = 40 * $ratio;
            } elseif ($numberFormat > 150.0 && $numberFormat <= 300.0) {
                $specificSharePopulationResult = 70 * $ratio;
            } else {
                $specificSharePopulationResult = 100 * $ratio;
            }
        } else {
            if ($specificSharePopulation <= 2.0) {
                $specificSharePopulationResult = 25 * $ratio;
            } elseif ($specificSharePopulation > 2.0 && $specificSharePopulation <= 4.0) {
                $specificSharePopulationResult = 50 * $ratio;
            } elseif ($specificSharePopulation > 4.0 && $specificSharePopulation <= 5.0) {
                $specificSharePopulationResult = 75 * $ratio;
            } else {
                $specificSharePopulationResult = 100 * $ratio;
            }
        }
        $this->showDump($specificSharePopulationResult, 4);
        $result += $specificSharePopulationResult;

        $ratio = 0.10;
        $populationResult = 0;
        if (!empty($model->population_in_project_implementation) || $model->participation_population_in_implementation_project->isNotEmpty()) {
            $populationResult = 100 * $ratio;
        }
        $this->showDump($populationResult, 5);
        $result += $populationResult;

        // наличие источников финансирования мероприятий по эксплуатации и содержанию муниципального имущества, предусмотренного проектом, после его завершения
        $ratio = 0.10;

        $filtering = $model->operating_and_maintenance_costs->filter(function ($query) {
            if(!empty($query->field3) || !empty($query->field4) || !empty($query->field5) || !empty($query->field6)) {
                return $query;
            }
        });

        $operatingAndMaintenanceCostsResult = 0;
        if (!!count($filtering)) {
            $operatingAndMaintenanceCostsResult = 100 * $ratio;
        }
        $this->showDump($operatingAndMaintenanceCostsResult, 6);
        $result += $operatingAndMaintenanceCostsResult;

        $ratio = 0.05;
        $populationInProjectResult = 0;
        if (!empty($model->population_in_project_provision) || $model->public_participation_in_operation_facility->isNotEmpty()) {

            $populationInProjectResult = 100 * $ratio;
        }
        $this->showDump($populationInProjectResult, 7);
        $result += $populationInProjectResult;

        $ratio = 0.05;
        $questionnairesResult = 0;
        if ($model->questionnaires->isNotEmpty()) {
            $questionnairesResult = 100 * $ratio;
        }
        $this->showDump($questionnairesResult, 8);
        $result += $questionnairesResult;

        $ratio = 0.05;
        $massMediaResult = 0;
        if ($model->mass_media->isNotEmpty() || $model->project_implementation_provides_informational_support->isNotEmpty()) {
            $massMediaResult = 100 * $ratio;
        }
        $this->showDump($massMediaResult, 9);
        $result += $massMediaResult;

        $this->showDD($result, 'Итого');

        return $result;
    }

    /**
     * --------------------------------------
     * Получаем расчет муниципального района
     * --------------------------------------
     *
     * @param PPMIApplication $model
     * @param int $result
     * @return float|int
     */
    public function getMRCalculation(PPMIApplication $model, $result = 0): float|int
    {
        $projectCost = $this->getProjectCost($model);

        if (!$projectCost) {
            return 0;
        }

        // превышение уровня софинансирования проекта за счет бюджета муниципального образования (в процентных пунктах)
        $significantPercentage = (float) number_format((($model->funds_municipal / $projectCost * 100) - 20), 1, '.', '');

        $ratio = 0.2;
        if ($significantPercentage < 0.1) {
            $significantPercentageResult = 0;
        } elseif ($significantPercentage > 0.0 && $significantPercentage <= 5.0) {
            $significantPercentageResult =  40 * $ratio;
        } elseif ($significantPercentage > 5.0 && $significantPercentage <= 10.0) {
            $significantPercentageResult =  60 * $ratio;
        } elseif ($significantPercentage > 10.0 && $significantPercentage <= 15.0) {
            $significantPercentageResult =  80 * $ratio;
        } else {
            $significantPercentageResult =  100 * $ratio;
        }
        $this->showDump($significantPercentageResult, 1);
        $result += $significantPercentageResult;

        $individualsAndLegalEntitiesFunds = $this->getIndividualsAndLegalEntitiesFunds($model);

        // уровень софинансирования проекта за счет средств физических и юридических лиц в денежной форме (процентов от предполагаемой стоимости проекта)
        $levelCoFinancingOfProject = (float) number_format(($individualsAndLegalEntitiesFunds / $projectCost * 100), 1, '.', '');
        $ratio = 0.3;
        $max = $this->isDistrictPlusGp($model) ? 2.0 : 5.0;
        if ($levelCoFinancingOfProject < $max || $levelCoFinancingOfProject < 1.0) {
            $levelCoFinancingOfProjectResult = 0;
        } elseif ($levelCoFinancingOfProject >= 1.0 && $levelCoFinancingOfProject <= 1.99) {
            $levelCoFinancingOfProjectResult =  50 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 2.0 && $levelCoFinancingOfProject <= 5.0) {
            $levelCoFinancingOfProjectResult = 60 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 5.1 && $levelCoFinancingOfProject <= 7.0) {
            $levelCoFinancingOfProjectResult = 70 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 7.1 && $levelCoFinancingOfProject <= 10.0) {
            $levelCoFinancingOfProjectResult = 80 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 10.1 && $levelCoFinancingOfProject <= 14.0) {
            $levelCoFinancingOfProjectResult = 90 * $ratio;
        } else {
            $levelCoFinancingOfProjectResult = 100 * $ratio;
        }
        $this->showDump($levelCoFinancingOfProjectResult, 2);
        $result += $levelCoFinancingOfProjectResult;

        // удельный вес (доля) населения, которое будет регулярно пользоваться результатами от реализации проекта
        $specificSharePopulation = (float) number_format((($model->population_size / $model->population_size_settlement * 100)), 1, '.', '');
        $ratio = 0.05;
        if ($specificSharePopulation <= 5.0) {
            $specificSharePopulationResult = 40 * $ratio;
        } elseif ($specificSharePopulation > 5.0 && $specificSharePopulation <= 30.0) {
            $specificSharePopulationResult = 60 * $ratio;
        } elseif ($specificSharePopulation > 30.0 && $specificSharePopulation <= 50.0) {
            $specificSharePopulationResult = 80 * $ratio;
        } else {
            $specificSharePopulationResult = 100 * $ratio;
        }
        $this->showDump($specificSharePopulationResult, 3);
        $result += $specificSharePopulationResult;

        // участие населения в определении проблемы и выборе проекта согласно протоколу собрания граждан
        $ratio = 0.10;
        $specificSharePopulation = (float) number_format((($model->population_size_in_congregation / $model->population_size_settlement * 100)), 1, '.', '');
        $numberFormat = (float) number_format(($model->population_size_in_congregation), 1, '.', '');
        if ($model->population_size_settlement > 4000) {
            if ($numberFormat <= 50.0) {
                $specificSharePopulationResult = 25 * $ratio;
            } elseif ($numberFormat > 50.0 && $numberFormat <= 150.0) {
                $specificSharePopulationResult = 40 * $ratio;
            } elseif ($numberFormat > 150.0 && $numberFormat <= 300.0) {
                $specificSharePopulationResult = 70 * $ratio;
            } else {
                $specificSharePopulationResult = 100 * $ratio;
            }
        } else {
            if ($specificSharePopulation <= 2.0) {
                $specificSharePopulationResult = 25 * $ratio;
            } elseif ($specificSharePopulation > 2.0 && $specificSharePopulation <= 4.0) {
                $specificSharePopulationResult = 50 * $ratio;
            } elseif ($specificSharePopulation > 4.0 && $specificSharePopulation <= 5.0) {
                $specificSharePopulationResult = 75 * $ratio;
            } else {
                $specificSharePopulationResult = 100 * $ratio;
            }
        }
        $this->showDump($specificSharePopulationResult, 4);
        $result += $specificSharePopulationResult;

        $ratio = 0.10;
        $populationResult = 0;
        if (!empty($model->population_in_project_implementation) || $model->participation_population_in_implementation_project->isNotEmpty()) {
            $populationResult = 100 * $ratio;
        }
        $this->showDump($populationResult, 5);
        $result += $populationResult;

        // наличие источников финансирования мероприятий по эксплуатации и содержанию муниципального имущества, предусмотренного проектом, после его завершения
        $ratio = 0.10;
        $filtering = $model->operating_and_maintenance_costs->filter(function ($query) {
            if(!empty($query->field3) || !empty($query->field4) || !empty($query->field5) || !empty($query->field6)) {
                return $query;
            }
        });

        $operatingAndMaintenanceCostsResult = 0;
        if (!!count($filtering)) {
            $operatingAndMaintenanceCostsResult = 100 * $ratio;
        }
        $this->showDump($operatingAndMaintenanceCostsResult, 6);
        $result += $operatingAndMaintenanceCostsResult;

        $ratio = 0.05;
        $populationInProjectResult = 0;
        if (!empty($model->population_in_project_provision) || $model->public_participation_in_operation_facility->isNotEmpty()) {
            $populationInProjectResult = 100 * $ratio;
        }
        $this->showDump($populationInProjectResult, 7);
        $result += $populationInProjectResult;

        $ratio = 0.05;
        $questionnairesResult = 0;
        if ($model->questionnaires->isNotEmpty()) {
            $questionnairesResult = 100 * $ratio;
        }
        $this->showDump($questionnairesResult, 8);
        $result += $questionnairesResult;

        $ratio = 0.05;
        $massMediaResult = 0;
        if ($model->mass_media->isNotEmpty() || $model->project_implementation_provides_informational_support->isNotEmpty()) {
            $massMediaResult = 100 * $ratio;
        }
        $this->showDump($massMediaResult, 9);
        $result += $massMediaResult;

        $this->showDD($result, 'Итого');

        return $result;
    }

    /**
     * -------------------------------------
     * Получаем расчет городского поселения
     * -------------------------------------
     *
     * @param PPMIApplication $model
     * @param int $result
     * @return float|int
     */
    public function getGPCalculation(PPMIApplication $model, $result = 0)
    {
        // Общая стоимость проекта
        $projectCost = $this->getProjectCost($model);

        if (!$projectCost) {
            return 0;
        }

        // Превышение уровня софинансирования проекта за счет бюджета муниципального образования (в процентных пунктах)
        $significantPercentage = (float) number_format((($model->funds_municipal / $projectCost * 100) - 20), 1, '.', '');
        $ratio = 0.2;
        if ($significantPercentage < 0.1) {
            $significantPercentageResult = 0;
        } elseif ($significantPercentage > 0.0 && $significantPercentage <= 5.0) {
            $significantPercentageResult = 40 * $ratio;
        } elseif ($significantPercentage > 5.0 && $significantPercentage <= 10.0) {
            $significantPercentageResult = 60 * $ratio;
        } elseif ($significantPercentage > 10.0 && $significantPercentage <= 15.0) {
            $significantPercentageResult = 80 * $ratio;
        } else {
            $significantPercentageResult = 100 * $ratio;
        }
        $this->showDump($significantPercentageResult, 1);
        $result += $significantPercentageResult;

        $individualsAndLegalEntitiesFunds = $this->getIndividualsAndLegalEntitiesFunds($model);

        // Уровень софинансирования проекта за счет средств физических и юридических лиц в денежной форме (процентов от предполагаемой стоимости проекта)
        $levelCoFinancingOfProject = (float) number_format(($individualsAndLegalEntitiesFunds / $projectCost * 100), 1, '.', '');
        $ratio = 0.3;
        $max = $this->isDistrictPlusGp($model) ? 2.0 : 5.0;
        if ($levelCoFinancingOfProject < $max || $levelCoFinancingOfProject < 1.0) {
            $levelCoFinancingOfProjectResult = 0;
        } elseif ($levelCoFinancingOfProject >= 1.0 && $levelCoFinancingOfProject <= 1.99) {
            $levelCoFinancingOfProjectResult = 50 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 2.0 && $levelCoFinancingOfProject <= 5.0) {
            $levelCoFinancingOfProjectResult = 60 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 5.1 && $levelCoFinancingOfProject <= 7.0) {
            $levelCoFinancingOfProjectResult = 70 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 7.1 && $levelCoFinancingOfProject <= 10.0) {
            $levelCoFinancingOfProjectResult = 80 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 10.1 && $levelCoFinancingOfProject <= 14.0) {
            $levelCoFinancingOfProjectResult = 90 * $ratio;
        } else {
            $levelCoFinancingOfProjectResult = 100 * $ratio;
        }
        $this->showDump($levelCoFinancingOfProjectResult, 2);
        $result += $levelCoFinancingOfProjectResult;

        // удельный вес (доля) населения, которое будет регулярно пользоваться результатами от реализации проекта
        $specificSharePopulation = (float) number_format((($model->population_size / $model->population_size_settlement * 100)), 1, '.', '');
        $ratio = 0.05;
        if ($specificSharePopulation <= 5.0) {
            $specificSharePopulationResult = 40 * $ratio;
        } elseif ($specificSharePopulation > 5.0 && $specificSharePopulation <= 30.0) {
            $specificSharePopulationResult = 60 * $ratio;
        } elseif ($specificSharePopulation > 30.0 && $specificSharePopulation <= 50.0) {
            $specificSharePopulationResult = 80 * $ratio;
        } else {
            $specificSharePopulationResult = 100 * $ratio;
        }
        $this->showDump($specificSharePopulationResult, 3);
        $result += $specificSharePopulationResult;

        // Участие населения в определении проблемы и выборе проекта согласно протоколу собрания граждан
        $specificSharePopulation = (float) number_format((($model->population_size_in_congregation / $model->population_size_settlement * 100)), 1, '.', '');
        $numberFormat = (float) number_format(($model->population_size_in_congregation), 1, '.', '');
        $ratio = 0.10;
        if ($model->population_size_settlement > 4000) {
            if ($numberFormat <= 50.0) {
                $specificSharePopulationResult = 25 * $ratio;
            } elseif ($numberFormat > 50.0 && $numberFormat <= 150.0) {
                $specificSharePopulationResult = 40 * $ratio;
            } elseif ($numberFormat > 150.0 && $numberFormat <= 300.0) {
                $specificSharePopulationResult = 70 * $ratio;
            } else {
                $specificSharePopulationResult = 100 * $ratio;
            }
        } else {
            if ($specificSharePopulation <= 2.0) {
                $specificSharePopulationResult = 25 * $ratio;
            } elseif ($specificSharePopulation > 2.0 && $specificSharePopulation <= 4.0) {
                $specificSharePopulationResult = 50 * $ratio;
            } elseif ($specificSharePopulation > 4.0 && $specificSharePopulation <= 8.0) {
                $specificSharePopulationResult = 75 * $ratio;
            } else {
                $specificSharePopulationResult = 100 * $ratio;
            }
        }
        $this->showDump($specificSharePopulationResult, 4);
        $result += $specificSharePopulationResult;

        // Участие населения в реализации проекта
        $ratio = 0.10;
        $populationResult = 0;
        if (!empty($model->population_in_project_implementation) || $model->participation_population_in_implementation_project->isNotEmpty()) {
            $populationResult = 100 * $ratio;
        }
        $this->showDump($populationResult, 5);
        $result += $populationResult;

        // Наличие источников финансирования мероприятий по эксплуатации и содержанию муниципального имущества, предусмотренного проектом, после его завершения
        $ratio = 0.10;
        $filtering = $model->operating_and_maintenance_costs->filter(function ($query) {
            if(!empty($query->field3) || !empty($query->field4) || !empty($query->field5) || !empty($query->field6)) {
                return $query;
            }
        });

        $operatingAndMaintenanceCostsResult = 0;
        if (!!count($filtering)) {
            $operatingAndMaintenanceCostsResult = 100 * $ratio;
        }
        $this->showDump($operatingAndMaintenanceCostsResult, 6);
        $result += $operatingAndMaintenanceCostsResult;

        // Участие населения в обеспечении эксплуатации и содержании муниципального имуществ
        $ratio = 0.05;
        $populationInProjectResult = 0;
        if (!empty($model->population_in_project_provision) || $model->public_participation_in_operation_facility->isNotEmpty()) {
            $populationInProjectResult = 100 * $ratio;
        }
        $this->showDump($populationInProjectResult, 7);
        $result += $populationInProjectResult;

        // Предварительное обсуждение проекта
        $ratio = 0.05;
        $questionnairesResult = 0;
        if ($model->questionnaires->isNotEmpty()) {
            $questionnairesResult = 100 * $ratio;
        }
        $this->showDump($questionnairesResult, 8);
        $result += $questionnairesResult;

        $ratio = 0.05;
        // Участие СМИ
        $massMediaResult = 0;
        if ($model->mass_media->isNotEmpty() || $model->project_implementation_provides_informational_support->isNotEmpty()) {
            $massMediaResult = 100 * $ratio;
        }
        $this->showDump($massMediaResult, 9);
        $result += $massMediaResult;

        $this->showDD($result, 'Итого');

        return $result;
    }

    /**
     * ----------------------------------
     * Получаем расчет городского округа
     * ----------------------------------
     *
     * @param PPMIApplication $model
     * @param int $result
     * @return float|int
     */
    public function getGOCalculation(PPMIApplication $model, $result = 0)
    {
        $projectCost = $this->getProjectCost($model);

        if (!$projectCost) {
            return 0;
        }

        // превышение уровня софинансирования проекта за счет бюджета муниципального образования (в процентных пунктах)
        $significantPercentage = (float) number_format((($model->funds_municipal / $projectCost * 100) - 20), 1, '.', '');
        $ratio = 0.2;
        if ($significantPercentage < 0.1) {
            $significantPercentageResult = 0;
        } elseif ($significantPercentage > 0.0 && $significantPercentage <= 5.0) {
            $significantPercentageResult = 40 * $ratio;
        } elseif ($significantPercentage > 5.0 && $significantPercentage <= 10.0) {
            $significantPercentageResult = 60 * $ratio;
        } elseif ($significantPercentage > 10.0 && $significantPercentage <= 15.0) {
            $significantPercentageResult = 80 * $ratio;
        } else {
            $significantPercentageResult = 100 * $ratio;
        }
        $this->showDump($significantPercentageResult, 1);
        $result += $significantPercentageResult;

        $individualsAndLegalEntitiesFunds = $this->getIndividualsAndLegalEntitiesFunds($model);

        // уровень софинансирования проекта за счет средств физических и юридических лиц в денежной форме (процентов от предполагаемой стоимости проекта)
        $levelCoFinancingOfProject = (float) number_format(($individualsAndLegalEntitiesFunds / $projectCost * 100), 1, '.', '');
        $ratio = 0.3;
        // Доля безвозмездных поступления в бюджет городских округов
        if ($levelCoFinancingOfProject < 5.0) {
            $levelCoFinancingOfProjectResult = 0;
        } elseif ($levelCoFinancingOfProject >= 5.0 && $levelCoFinancingOfProject <= 12.0) {
            $levelCoFinancingOfProjectResult = 70 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 12.1 && $levelCoFinancingOfProject <= 15.0) {
            $levelCoFinancingOfProjectResult = 80 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 15.1 && $levelCoFinancingOfProject <= 17.0) {
            $levelCoFinancingOfProjectResult = 90 * $ratio;
        } else {
            $levelCoFinancingOfProjectResult = 100 * $ratio;
        }
        $this->showDump($levelCoFinancingOfProjectResult, 2);
        $result += $levelCoFinancingOfProjectResult;

        // удельный вес (доля) населения, которое будет регулярно пользоваться результатами от реализации проекта
        $specificSharePopulation = (float) number_format((($model->population_size / $model->population_size_settlement * 100)), 1, '.', '');
        $ratio = 0.05;
        if ($specificSharePopulation <= 5.0) {
            $specificSharePopulationResult = 40 * $ratio;
        } elseif ($specificSharePopulation > 5.0 && $specificSharePopulation <= 30.0) {
            $specificSharePopulationResult = 60 * $ratio;
        } elseif ($specificSharePopulation > 30.0 && $specificSharePopulation <= 50.0) {
            $specificSharePopulationResult = 80 * $ratio;
        } else {
            $specificSharePopulationResult = 100 * $ratio;
        }
        $this->showDump($specificSharePopulationResult, 3);
        $result += $specificSharePopulationResult;

        // участие населения в определении проблемы и выборе проекта согласно протоколу собрания граждан

        $ratio = 0.10;
        $specificSharePopulation = (float) number_format((($model->population_size_in_congregation / $model->population_size_settlement * 100)), 1, '.', '');
        $numberFormat = (float) number_format(($model->population_size_in_congregation), 1, '.', '');
        if ($model->population_size_settlement > 4000) {
            if ($numberFormat <= 50.0) {
                $specificSharePopulationResult = 25 * $ratio;
            } elseif ($numberFormat > 50.0 && $numberFormat <= 150.0) {
                $specificSharePopulationResult = 40 * $ratio;
            } elseif ($numberFormat > 150.0 && $numberFormat <= 300.0) {
                $specificSharePopulationResult = 70 * $ratio;
            } else {
                $specificSharePopulationResult = 100 * $ratio;
            }
        } else {
            if ($specificSharePopulation <= 2.0) {
                $specificSharePopulationResult = 25 * $ratio;
            } elseif ($specificSharePopulation > 2.0 && $specificSharePopulation <= 4.0) {
                $specificSharePopulationResult = 50 * $ratio;
            } elseif ($specificSharePopulation > 4.0 && $specificSharePopulation <= 8.0) {
                $specificSharePopulationResult = 75 * $ratio;
            } else {
                $specificSharePopulationResult = 100 * $ratio;
            }
        }
        $this->showDump($specificSharePopulationResult, 4);
        $result += $specificSharePopulationResult;

        $ratio = 0.10;
        $populationResult = 0;
        if (!empty($model->population_in_project_implementation) || $model->participation_population_in_implementation_project->isNotEmpty()) {
            $populationResult = 100 * $ratio;
        }
        $this->showDump($populationResult, 5);
        $result += $populationResult;

        // наличие источников финансирования мероприятий по эксплуатации и содержанию муниципального имущества, предусмотренного проектом, после его завершения
        $ratio = 0.10;

        $filtering = $model->operating_and_maintenance_costs->filter(function ($query) {
            if(!empty($query->field3) || !empty($query->field4) || !empty($query->field5) || !empty($query->field6)) {
                return $query;
            }
        });

        $operatingAndMaintenanceCostsResult = 0;
        if (!!count($filtering)) {
            $operatingAndMaintenanceCostsResult = 100 * $ratio;
        }
        $this->showDump($operatingAndMaintenanceCostsResult, 6);
        $result += $operatingAndMaintenanceCostsResult;

        $ratio = 0.05;
        $populationInProjectResult = 0;
        if (!empty($model->population_in_project_provision) || $model->public_participation_in_operation_facility->isNotEmpty()) {
            $populationInProjectResult = 100 * $ratio;
        }
        $this->showDump($populationInProjectResult, 7);
        $result += $populationInProjectResult;

        $ratio = 0.05;
        $questionnairesResult = 0;
        if ($model->questionnaires->isNotEmpty()) {
            $questionnairesResult = 100 * $ratio;
        }
        $this->showDump($questionnairesResult, 8);
        $result += $questionnairesResult;

        $ratio = 0.05;
        $massMediaResult = 0;
        if ($model->mass_media->isNotEmpty() || $model->project_implementation_provides_informational_support->isNotEmpty()) {
            $massMediaResult = 100 * $ratio;
        }
        $this->showDump($massMediaResult, 9);
        $result += $massMediaResult;

        $this->showDD($result, 'Итого');

        return $result;
    }

    /**
     * ------------------------------------
     * Получаем расчет сельского поселения
     * ------------------------------------
     *
     * @param PPMIApplication $model
     * @param int $result
     * @return float|int
     */
    public function getSPCalculation(PPMIApplication $model, $result = 0)
    {
        $projectCost = $this->getProjectCost($model);

        if (!$projectCost) {
            return 0;
        }

        // превышение уровня софинансирования проекта за счет бюджета муниципального образования (в процентных пунктах)
        $significantPercentage = (float) number_format((($model->funds_municipal / $projectCost * 100) - 10), 1, '.', '');
        $ratio = 0.2;
        if ($significantPercentage < 0.1) {
            $significantPercentageResult = 0;
        } elseif ($significantPercentage > 0.0 && $significantPercentage <= 5.0) {
            $significantPercentageResult = 40 * $ratio;
        } elseif ($significantPercentage > 5.0 && $significantPercentage <= 10.0) {
            $significantPercentageResult = 60 * $ratio;
        } elseif ($significantPercentage > 10.0 && $significantPercentage <= 15.0) {
            $significantPercentageResult = 80 * $ratio;
        } else {
            $significantPercentageResult = 100 * $ratio;
        }
        $this->showDump($significantPercentageResult, 1);
        $result += $significantPercentageResult;

        $individualsAndLegalEntitiesFunds = $this->getIndividualsAndLegalEntitiesFunds($model);

        // уровень софинансирования проекта за счет средств физических и юридических лиц в денежной форме (процентов от предполагаемой стоимости проекта)
        $levelCoFinancingOfProject = (float) number_format(($individualsAndLegalEntitiesFunds / $projectCost * 100), 1, '.', '');
        $ratio = 0.3;
        $max = $model->population_size_settlement > 1000 ? 5.0 : 2.0;
        if ($levelCoFinancingOfProject < $max || $levelCoFinancingOfProject < 1.0) {
            $levelCoFinancingOfProjectResult = 0;
        } elseif ($levelCoFinancingOfProject >= 1.0 && $levelCoFinancingOfProject <= 1.99) {
            $levelCoFinancingOfProjectResult = 50 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 2.0 && $levelCoFinancingOfProject <= 5.0) {
            $levelCoFinancingOfProjectResult = 60 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 5.1 && $levelCoFinancingOfProject <= 7.0) {
            $levelCoFinancingOfProjectResult = 70 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 7.1 && $levelCoFinancingOfProject <= 10.0) {
            $levelCoFinancingOfProjectResult = 80 * $ratio;
        } elseif ($levelCoFinancingOfProject >= 10.1 && $levelCoFinancingOfProject <= 14.0) {
            $levelCoFinancingOfProjectResult = 90 * $ratio;
        } else {
            $levelCoFinancingOfProjectResult = 100 * $ratio;
        }
        $this->showDump($levelCoFinancingOfProjectResult, 2);
        $result += $levelCoFinancingOfProjectResult;

        // удельный вес (доля) населения, которое будет регулярно пользоваться результатами от реализации проекта
        $specificSharePopulation = (float) number_format((($model->population_size / $model->population_size_settlement * 100)), 1, '.', '');
        $ratio = 0.05;
        if ($specificSharePopulation <= 5.0) {
            $specificSharePopulationResult = 40 * $ratio;
        } elseif ($specificSharePopulation > 5.0 && $specificSharePopulation <= 30.0) {
            $specificSharePopulationResult = 60 * $ratio;
        } elseif ($specificSharePopulation > 30.0 && $specificSharePopulation <= 50.0) {
            $specificSharePopulationResult = 80 * $ratio;
        } else {
            $specificSharePopulationResult = 100 * $ratio;
        }
        $this->showDump($specificSharePopulationResult, 3);
        $result += $specificSharePopulationResult;

        // участие населения в определении проблемы и выборе проекта согласно протоколу собрания граждан

        $ratio = 0.10;
        $specificSharePopulation = (float) number_format((($model->population_size_in_congregation / $model->population_size_settlement * 100)), 1, '.', '');
        $numberFormat = (float) number_format(($model->population_size_in_congregation), 1, '.', '');
        if ($model->population_size_settlement > 4000) {
            if ($numberFormat <= 50.0) {
                $specificSharePopulationResult = 25 * $ratio;
            } elseif ($numberFormat > 50.0 && $numberFormat <= 150.0) {
                $specificSharePopulationResult = 40 * $ratio;
            } elseif ($numberFormat > 150.0 && $numberFormat <= 300.0) {
                $specificSharePopulationResult = 70 * $ratio;
            } else {
                $specificSharePopulationResult = 100 * $ratio;
            }
        } else {
            if ($specificSharePopulation <= 2.0) {
                $specificSharePopulationResult = 25 * $ratio;
            } elseif ($specificSharePopulation > 2.0 && $specificSharePopulation <= 4.0) {
                $specificSharePopulationResult = 50 * $ratio;
            } elseif ($specificSharePopulation > 4.0 && $specificSharePopulation <= 8.0) {
                $specificSharePopulationResult = 75 * $ratio;
            } else {
                $specificSharePopulationResult = 100 * $ratio;
            }
        }
        $this->showDump($specificSharePopulationResult, 4);
        $result += $specificSharePopulationResult;

        $ratio = 0.10;
        $populationResult = 0;
        if (!empty($model->population_in_project_implementation) || $model->participation_population_in_implementation_project->isNotEmpty()) {
            $populationResult = 100 * $ratio;
        }
        $this->showDump($populationResult, 5);
        $result += $populationResult;

        // наличие источников финансирования мероприятий по эксплуатации и содержанию муниципального имущества, предусмотренного проектом, после его завершения
        $ratio = 0.10;

        $filtering = $model->operating_and_maintenance_costs->filter(function ($query) {
            if(!empty($query->field3) || !empty($query->field4) || !empty($query->field5) || !empty($query->field6)) {
                return $query;
            }
        });

        $operatingAndMaintenanceCostsResult = 0;
        if (!!count($filtering)) {
            $operatingAndMaintenanceCostsResult = 100 * $ratio;
        }
        $this->showDump($operatingAndMaintenanceCostsResult, 6);
        $result += $operatingAndMaintenanceCostsResult;

        $ratio = 0.05;
        $populationInProjectResult = 0;
        if (!empty($model->population_in_project_provision) || $model->public_participation_in_operation_facility->isNotEmpty()) {
            $populationInProjectResult = 100 * $ratio;
        }
        $this->showDump($populationInProjectResult, 7);
        $result += $populationInProjectResult;

        $ratio = 0.05;
        $questionnairesResult = 0;
        if ($model->questionnaires->isNotEmpty()) {
            $questionnairesResult = 100 * $ratio;
        }
        $this->showDump($questionnairesResult, 8);
        $result += $questionnairesResult;

        $ratio = 0.05;
        $massMediaResult = 0;
        if ($model->mass_media->isNotEmpty() || $model->project_implementation_provides_informational_support->isNotEmpty()) {
            $massMediaResult = 100 * $ratio;
        }
        $this->showDump($massMediaResult, 9);
        $result += $massMediaResult;

        $this->showDD($result, 'Итого');

        return $result;
    }

    /**
     * ----------------------------------------
     * Получаем расчет национального поселения
     * ----------------------------------------
     *
     * @param PPMIApplication $model
     * @param int $result
     * @return int
     */
    public function getNPCalculation(PPMIApplication $model, int $result = 0)
    {
        return $result;
    }

    public function getProjectCost(PPMIApplication $model)
    {
        return ($model->funds_municipal + $model->funds_individuals + $model->funds_legal_entities + $model->funds_republic);
    }

    public function getIndividualsAndLegalEntitiesFunds(PPMIApplication $model)
    {
        return ($model->funds_individuals + $model->funds_legal_entities);
    }

    public function isDistrictPlusGp(PPMIApplication $model): bool
    {
        return !!$model->user?->municipality?->is_district_plus_gp || !!$model->user?->municipality?->parent?->is_district_plus_gp;
    }
}
