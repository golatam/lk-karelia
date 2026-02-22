<?php
namespace Database\Seeders;

use App\Models\MostBeautifulVillage;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Output\ConsoleOutput;

class MostBeautifulVillageTableSeeder extends Seeder
{
    public $actions = [
        'view',
        'create',
        'update',
        'delete',
        'restore',
    ];

    public $output;

    public $model;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {

            $this->model = (new MostBeautifulVillage());

            $this->output = new ConsoleOutput();

            $this->setPermissions($this->model, $this->output, $this->actions);
        } catch (\Exception $e) {

            $this->output->writeln("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
        }
    }

    public function setPermissions($model, $output, $actions, $counter = 0)
    {
        $tableName = $model->getTable();

        foreach ($actions as $action) {

            $type = 'core';
            $group = \Illuminate\Support\Str::singular($tableName);

            $name = __("{$tableName}_applications.permissions.name.{$action}", [], app()->getLocale());
            $description = __("{$tableName}_applications.permissions.descriptions.{$action}");

            $permission = \App\Models\Permission::firstOrNew([
                'type' => $type,
                'group' => $group,
                'action' => $action,
            ]);

            if (!$permission->exists) {

                $permission->name = $name;
                $permission->description = $description;
                $permission->save();
                $counter++;
            }
        }

        if ($counter) {

            $output->writeln("<info>Установлены новые разрешения:</info>  {$counter}");
        } else {

            $output->writeln("<info>Новые разрешения отсутствуют.</info>");
        }
    }
}
