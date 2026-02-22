<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Symfony\Component\Console\Output\ConsoleOutput;

class OtherTableSeeder extends Seeder
{
    public $actions = [
        'show_admin',
        'show_committee',
        'show_user',
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
        $this->output = new ConsoleOutput();

        try {

            $this->set_permissions();
        } catch (\Exception $e) {

            $errorMessage = $e->getMessage();
            $this->output->writeln("<error>{$errorMessage}</error> ");
        }
    }

    public function set_permissions($counter = 0)
    {
        $tableName = 'other';

        foreach ($this->actions as $action) {

            $type = 'core';
            $group = \Illuminate\Support\Str::singular($tableName);

            $name = __("{$tableName}.permissions.name.{$action}", [], app()->getLocale());
            $description = __("{$tableName}.permissions.descriptions.{$action}");

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

            $this->output->writeln("<info>Установлены новые разрешения:</info>  {$counter}");
        } else {

            $this->output->writeln("<info>Новые разрешения отсутствуют.</info>");
        }
    }
}
