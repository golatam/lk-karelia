<?php

namespace App\Console\Commands;

use App\Models\ApplicationScoreColumn;
use Illuminate\Console\Command;

class AddApplicationScoreColumnCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'application:add-score-column {--most_beautiful_villages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавление столбцов для оценки в базу данных';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('most_beautiful_villages')) {

            $this->mostBeautifulVillages();

            return 0;
        } else {

            $this->all();

            return 0;
        }
    }

    public function all()
    {
        $this->mostBeautifulVillages();
    }

    public function mostBeautifulVillages($i = 0)
    {
        try {

            $string = file_get_contents(public_path("import/applications.json"));
            $jsonData = json_decode($string, true);

            if (isset($jsonData['most_beautiful_village_fields']) && is_array($jsonData['most_beautiful_village_fields'])) {

                $progress = $this->output->createProgressBar(count($jsonData['most_beautiful_village_fields']));

                foreach ($jsonData['most_beautiful_village_fields'] as $jsonDatum) {

                    $applicationScoreColumn = ApplicationScoreColumn::firstOrNew([
                        'name' => trim($jsonDatum['name']),
                        'application_type' => $jsonDatum['application_type'],
                    ]);

                    if (!$applicationScoreColumn->exists) {

                        $i++;
                    }

                    $applicationScoreColumn->significance_factor = $jsonDatum['significance_factor'];
                    $applicationScoreColumn->max_rating = $jsonDatum['max_rating'];
                    $applicationScoreColumn->save();

                    $progress->advance();
                }

                $progress->finish();
                $this->output->newLine();

                $this->line("<fg=cyan>Импорт прошел успешно, добавлено {$i} полей из " . count($jsonData['most_beautiful_village_fields']));
            } else {

                $this->output->writeln("<fg=black;bg=yellow>Ошибка: Данные для импорта не найдены!</>");
            }
        } catch (\Exception $e) {

            $this->output->writeln("<fg=black;bg=yellow>Ошибка: {$e->getMessage()}</>");
        }
    }
}
