<?php

namespace App\Traits;

use App\Models\ApplicationTOS;

trait CalculationLPTOSApplicationTrait
{
    /**
     * -----------------------
     * Получаем расчет баллов
     * -----------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function getCalculation(ApplicationTOS $application, $result = 0): int
    {
        $result += $this->percentageResidentsInvolvement($application);

        $result += $this->numberPeopleBeneficiariesProject($application);

        $result += $this->numberImplementedPractices($application);

        $result += $this->validityAndRelevanceProblem($application);

        $result += $this->prospectAdditionalProjectImplementationWithoutAdditionalFunding($application);

        $result += $this->workScaleDoneOnProject($application);

        $result += $this->efficiencyProjectFinancialPerInhabitant($application);

        $result += $this->efficiencyProjectFinancialPerBeneficiary($application);

        $result += $this->volumeAttractedExtraBudgetaryFinancing($application);

        $result += $this->usingVolunteeringMechanisms($application);

        $result += $this->usingSocialPartnershipMechanisms($application);

        $result += $this->numberMeetingsHeld($application);

        $result += $this->coverageInformationAboutActivitiesAndAchievementsTOSInMedia($application);

        return $result;
    }

    /**
     * ------------------------------------------------------------------------------
     * Доля жителей вовлеченных в деятельность ТОС при реализации практики (проекта)
     * ------------------------------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function percentageResidentsInvolvement(ApplicationTOS $application, $result = 0): int
    {
        // 1
        $result += ((int) $application->calc_pri * 5);

        return $result;
    }

    /**
     * --------------------------------------------------------
     * Количество человек, проживающих в границах ТОС, которые
     * пользуются результатами Проекта (благополучатели)
     * --------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function numberPeopleBeneficiariesProject(ApplicationTOS $application, $result = 0): int
    {
        // 2
        $result += ((int) $application->calc_npbp * 4);

        return $result;
    }

    /**
     * ------------------------------------------------------------
     * Количество реализованных практик (проектов) и инициатив
     * ТОС за предыдущий год (кроме заявляемой практики (проекта))
     * ------------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function numberImplementedPractices(ApplicationTOS $application, $result = 0): int
    {
        // 3
        $result += ((int) $application->calc_nip * 3);

        return $result;
    }

    /**
     * ----------------------------------------------------------------------------
     * Обоснованность и актуальность проблемы, на решение которой направлен проект
     * ----------------------------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function validityAndRelevanceProblem(ApplicationTOS $application, $result = 0): int
    {
        // 4
        $result += ((int) $application->calc_varp * 2);

        return $result;
    }

    /**
     * -----------------------------------------------------------------------------------
     * Перспектива дополнительной реализации проекта (без дополнительного финансирования)
     * -----------------------------------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function prospectAdditionalProjectImplementationWithoutAdditionalFunding(ApplicationTOS $application, $result = 0): int
    {
        // 5
        $result += ((int) $application->calc_papiwaf * 3);

        return $result;
    }

    /**
     * -------------------------------------
     * Масштаб проделанных по проекту работ
     * -------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function workScaleDoneOnProject(ApplicationTOS $application, $result = 0): int
    {
        // 6
        $result += ((int) $application->calc_wsdop * 3);

        return $result;
    }

    /**
     * --------------------------------------------------
     * Финансовая эффективность проекта на одного жителя
     * --------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function efficiencyProjectFinancialPerInhabitant(ApplicationTOS $application, $result = 0): int
    {
        // 7.1
        $result += ((int) $application->calc_epfpi * 1);

        return $result;
    }

    /**
     * -----------------------------------------------------------
     * Финансовая эффективность проекта на одного благополучателя
     * -----------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function efficiencyProjectFinancialPerBeneficiary(ApplicationTOS $application, $result = 0): int
    {
        // 7.2
        $result += ((int) $application->calc_epfpb * 5);

        return $result;
    }

    /**
     * -----------------------------------------------------------------
     * Привлечение внебюджетных средств на осуществление практики
     * (проекта) ТОС, объемы привлеченного внебюджетного финансирования
     * -----------------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function volumeAttractedExtraBudgetaryFinancing(ApplicationTOS $application, $result = 0): int
    {
        // 8
        $result += ((int) $application->calc_vaebf * 5);

        return $result;
    }

    /**
     * -----------------------------------------------------------
     * Использование механизмов волонтерства (привлечение жителей
     * территории, на которой осуществляется проект, к выполнению
     * определенного перечня работ на безвозмездной основе)
     * -----------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function usingVolunteeringMechanisms(ApplicationTOS $application, $result = 0): int
    {
        // 9
        $result += ((int) $application->calc_uvm * 2);

        return $result;
    }

    /**
     * ------------------------------------------------------------------------------------
     * Использование механизмов социального партнерства (взаимодействие с органами
     * государственной власти, органами местного самоуправления муниципальных образований,
     * организациями и учреждениями, действующими на территории осуществления проекта)
     * ------------------------------------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function usingSocialPartnershipMechanisms(ApplicationTOS $application, $result = 0): int
    {
        // 10
        $result += ((int) $application->calc_uspm * 4);

        return $result;
    }

    /**
     * -------------------------------------------------------
     * Количество проведенных собраний (советов, конференций,
     * заседаний органов ТОС) и рассматриваемые вопросы.
     * -------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function numberMeetingsHeld(ApplicationTOS $application, $result = 0): int
    {
        // 11
        $result += ((int) $application->calc_nmh * 2);

        return $result;
    }

    /**
     * -----------------------------------------------------------------------------------
     * Освещение информации о деятельности и достижениях ТОС в средствах массовой
     * информации, в том числе в официальных группах (чатах) популярных социальных сетей
     * -----------------------------------------------------------------------------------
     *
     * @param ApplicationTOS $application
     * @param int $result
     * @return int
     */
    public function coverageInformationAboutActivitiesAndAchievementsTOSInMedia(ApplicationTOS $application, $result = 0): int
    {
        // 12
        $result += ((int) $application->calc_ciaaaatosim * 5);

        return $result;
    }
}
