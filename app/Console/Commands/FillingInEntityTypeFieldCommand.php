<?php

namespace App\Console\Commands;

use App\Models\ApplicationEstimate;
use App\Models\LPTOSApplication;
use Illuminate\Console\Command;

class FillingInEntityTypeFieldCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'application:filling-in-entity-type-field';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнение поля тип объекта';

    /**
     * Execute the console command.
     *
     * @param int $i
     * @return int
     */
    public function handle($i = 0)
    {
        try {

            $SZPTOSApplication = LPTOSApplication::make();

            $models = ApplicationEstimate::whereNull('entity_type')->get();

            $progress = $this->output->createProgressBar($models->count());

            foreach ($models as $model) {

                $model->entity_type = $SZPTOSApplication->getMorphClass();
                $model->save();

                $i++;

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<fg=cyan>Импорт прошел успешно, добавлено {$i} полей из {$models->count()}");

        } catch (\Exception $e) {

            $this->output->writeln("<fg=black;bg=yellow>Ошибка: {$e->getMessage()}</>");
        }
    }
}
