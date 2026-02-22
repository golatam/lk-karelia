<?php

namespace Database\Seeders;

use App\Models\ApplicationScoreColumn;
use Illuminate\Database\Seeder;

class ApplicationScoreColumnsSeeder extends Seeder
{
    public function run(): void
    {
        if (config('app.common.is_set_data_seed')) {

            ApplicationScoreColumn::create([
                'name' => 'Доля жителей вовлеченных в деятельность ТОС при реализации практики (проекта)',
                'significance_factor' => 5,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Количество человек, проживающих в границах
                                ТОС, которые пользуются результатами
                                Проекта (благополучатели)
                                ',
                'significance_factor' => 4,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Количество реализованных практик (проектов) и инициатив ТОС за предыдущий год (кроме заявляемой практики (проекта))',
                'significance_factor' => 3,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Обоснованность и актуальность проблемы, на решение которой направлен проект',
                'significance_factor' => 2,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Перспектива дополнительной реализации проекта (без дополнительного финансирования)',
                'significance_factor' => 3,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Масштаб проделанных по проекту работ',
                'significance_factor' => 3,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Финансовая эффективность проекта - на одного жителя',
                'significance_factor' => 1,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Финансовая эффективность проекта - на одного благополучателя',
                'significance_factor' => 5,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Привлечение внебюджетных средств на осуществление практики (проекта) ТОС, объемы привлеченного внебюджетного финансирования',
                'significance_factor' => 5,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Использование механизмов волонтерства (привлечение жителей территории, на которой осуществляется проект, к выполнению определенного перечня работ на безвозмездной основе)',
                'significance_factor' => 2,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Использование механизмов социального партнерства (взаимодействие с органами государственной власти, органами местного самоуправления муниципальных образований, организациями и учреждениями, действующими на территории осуществления проекта)',
                'significance_factor' => 4,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Количество проведенных собраний (советов, конференций, заседаний органов ТОС) и рассматриваемые вопросы.',
                'significance_factor' => 2,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);

            ApplicationScoreColumn::create([
                'name' => 'Освещение информации о деятельности и достижениях ТОС в средствах массовой информации, в том числе в официальных группах (чатах) популярных социальных сетей',
                'significance_factor' => 5,
                'max_rating' => 5,
                'application_type' => 'lptos_applications'
            ]);
        }
    }
}
