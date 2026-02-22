<?php

namespace App\Traits;

use App\Models\LTOSApplication;
use Carbon\Carbon;

trait CalculationLTOSApplicationTrait
{
    /**
     * -----------------------
     * Получаем расчет баллов
     * -----------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function getCalculation(LTOSApplication $application, $result = 0.00): float
    {
        $result += $this->organization_cultural_events($application);

        $result += $this->conducting_sports_competitions($application);

        $result += $this->drug_addiction_prevention_measures($application);

        $result += $this->availability_clubs($application);

        $result += $this->measures_organization_landscaping($application);

        $result += $this->number_objects_social_orientation($application);

        $result += $this->providing_assistance($application);

        $result += $this->healthy_lifestyle_corner($application);

        $result += $this->joint_preventive_measures($application);

        $result += $this->fire_prevention($application);

        $result += $this->meetings_and_seminars($application);

        $result += $this->placement_information_in_mass_media($application);

        $result += $this->participation_in_previous_contests_unsuccessful($application);

        $result += $this->participation_in_previous_contests_successful($application);

        $result += $this->awards($application);

        return $result;
    }

    /**
     * ---------------------------------------------------------------
     * Организация культурно-массовых мероприятий, праздников,
     * иных культурно-просветительных акций (1 за каждое мероприятие)
     * ---------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function organization_cultural_events(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->organization_cultural_events->filter(function ($query) {

            if(!empty($query->field22) || !empty($query->field23) || !empty($query->field24)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += (float) $item['field22'];
        }

        return $result;
    }

    /**
     * --------------------------------------------------------------
     * Проведение спортивных соревнований, гражданско-патриотических
     * игр, туристических выездов (1 за каждое мероприятие)
     * --------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function conducting_sports_competitions(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->conducting_sports_competitions->filter(function ($query) {

            if(!empty($query->field25) || !empty($query->field26) || !empty($query->field27)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += (float) $item['field25'];
        }

        return $result;
    }

    /**
     * ---------------------------------------------------------------------------
     * Проведение мероприятий, направленных на профилактику наркомании,
     * алкоголизма и формирование здорового образа жизни (1 за каждое мероприятие)
     * ---------------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function drug_addiction_prevention_measures(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->drug_addiction_prevention_measures->filter(function ($query) {

            if(!empty($query->field28) || !empty($query->field29) || !empty($query->field30)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += (float) $item['field28'];
        }

        return $result;
    }

    /**
     * -----------------------------------------------------------------------------
     * Наличие клубов, секций, кружков, организованных при ТОС (2 за каждую секцию)
     * -----------------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function availability_clubs(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->availability_clubs->filter(function ($query) {

            if(!empty($query->field31) || !empty($query->field32)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += 2;
        }

        return $result;
    }

    /**
     * ------------------------------------------------------------------
     * Проведение мероприятий по организации благоустройства и улучшения
     * санитарного состояния территории ТОС (1 за каждое мероприятие)
     * ------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function measures_organization_landscaping(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->measures_organization_landscaping->filter(function ($query) {

            if(!empty($query->field33) || !empty($query->field34)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += 1;
        }

        return $result;
    }

    /**
     * ------------------------------------------------------------------
     * Количество объектов социальной направленности, восстановленных,
     * отремонтированных или построенных силами ТОС (1 за каждый объект)
     * ------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function number_objects_social_orientation(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->number_objects_social_orientation->filter(function ($query) {

            if(!empty($query->field35) || !empty($query->field36) || !empty($query->field37)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += (int) $item['field35'];
        }

        return $result;
    }

    /**
     * ---------------------------------------------------------------------------------------------
     * Оказание помощи многодетным семьям, инвалидам одиноким пенсионерам, малоимущим гражданам - 1
     * ---------------------------------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function providing_assistance(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->providing_assistance->filter(function ($query) {

            if(!empty($query->field38) || !empty($query->field39)) {

                return $query;
            }
        });

        if (!empty($filtering)) {

            $result += 1;
        }

        return $result;
    }

    /**
     * ---------------------------------------------------------------------
     * Создание на территории ТОС уголка здорового образа жизни, разработка
     * буклетов, выпуск стенгазет по пропаганде здорового образа жизни - 2
     * ---------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function healthy_lifestyle_corner(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->healthy_lifestyle_corner->filter(function ($query) {

            if(!empty($query->field40) || !empty($query->field41)) {

                return $query;
            }
        });

        if (!empty($filtering)) {

            $result += 2;
        }

        return $result;
    }

    /**
     * ---------------------------------------------------------------------------------------
     * Участие членов ТОС в совместных с сотрудниками полиции профилактических мероприятиях,
     * связанных с профилактикой преступлений и иных правонарушений (1 за каждое мероприятие)
     * ---------------------------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function joint_preventive_measures(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->joint_preventive_measures->filter(function ($query) {

            if(!empty($query->field42) || !empty($query->field43) || !empty($query->field44)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += (int) $item['field42'];
        }

        return $result;
    }

    /**
     * -------------------------------------------------------------------------
     * Проведение мероприятий по профилактике пожаров (1 за каждое мероприятие)
     * -------------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function fire_prevention(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->fire_prevention->filter(function ($query) {

            if(!empty($query->field45) || !empty($query->field46) || !empty($query->field47)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += (int) $item['field45'];
        }

        return $result;
    }

    /**
     * -------------------------------------------------------------------------------------------------------------
     * Проведение ТОСами совещаний и семинаров с участием органов местного самоуправления (1 за каждое мероприятие)
     * -------------------------------------------------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function meetings_and_seminars(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->meetings_and_seminars->filter(function ($query) {

            if(!empty($query->field48) || !empty($query->field49) || !empty($query->field50)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += (int) $item['field48'];
        }

        return $result;
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * Размещение информации о деятельности ТОС по каждому направлению деятельности в средствах массовой
     * информации и в информационно-телекоммуникационной сети Интернет (0.00.5 за каждую статью, публикацию, выступление)
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function placement_information_in_mass_media(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->placement_information_in_mass_media->filter(function ($query) {

            if(!empty($query->field51) || !empty($query->field52) || !empty($query->field53)) {

                return $query;
            }
        });

        $applicationDateBorder = Carbon::create(2023, 11, 8, 23, 59, 59, config('app.timezone'));
        $applicationCreateDate = Carbon::parse($application->created_at);
        $applicationDateDiffInDays = $applicationDateBorder->diffInSeconds($applicationCreateDate, false);

        if ($applicationDateDiffInDays > 0) {
            if ($filtering->count() < 6) {
                $result = 1;
            } elseif ($filtering->count() > 5 && $filtering->count() < 21) {
                $result = 2;
            } elseif ($filtering->count() > 20 && $filtering->count() < 51) {
                $result = 3;
            } elseif ($filtering->count() > 50 && $filtering->count() < 101) {
                $result = 4;
            } else {
                $result = 5;
            }
        } else {
            foreach ($filtering as $item) {
                $result += ((is_int($item['field51']) ? $item['field51'] : 1) / 2);
            }
        }

        return $result;
    }

    /**
     * -----------------------------------------------------------------------
     * Участие ТОС в  конкурсах  проектов за предыдущие три года (1 за каждую
     * поданную, но не прошедшую конкурсный отбор,  проектную заявку)
     * -----------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function participation_in_previous_contests_unsuccessful(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->participation_in_previous_contests_unsuccessful->filter(function ($query) {

            if(!empty($query->field54) || !empty($query->field55)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += 1;
        }

        return $result;
    }

    /**
     * ------------------------------------------------------------------------------------
     * Участие ТОС в конкурсах,  за предыдущие три года (1 за каждый реализованный проект)
     * ------------------------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function participation_in_previous_contests_successful(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->participation_in_previous_contests_successful->filter(function ($query) {

            if(!empty($query->field56) || !empty($query->field57)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += 1;
        }

        return $result;
    }

    /**
     * -----------------------------------------------------------------------------------------------------
     * Награды ТОС и членов ТОС за тосовскую деятельность за предыдущие три года (0.00.2 за каждую награду)
     * -----------------------------------------------------------------------------------------------------
     *
     * @param LTOSApplication $application
     * @param float $result
     * @return float
     */
    public function awards(LTOSApplication $application, $result = 0.00): float
    {
        $filtering = $application->awards->filter(function ($query) {

            if(!empty($query->field58) || !empty($query->field59)) {

                return $query;
            }
        });

        foreach ($filtering as $item) {

            $result += 0.2;
        }

        return $result;
    }
}
