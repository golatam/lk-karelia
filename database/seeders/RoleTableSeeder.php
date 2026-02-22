<?php
namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class RoleTableSeeder extends Seeder
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

            $this->model = (new Role());

            $this->output = new ConsoleOutput();

            if (config('app.common.is_set_data_seed')) {

                DB::table('roles')->delete();

                DB::statement("ALTER TABLE `roles` AUTO_INCREMENT = 1;");

                $this->model->create([
                    "name" => "Супер пользователь",
                    "alias" => "su",
                    "description" => "Роль супер пользователя",
                ]);

                $this->model->create([
                    "name" => "Пользователь",
                    "alias" => "user",
                    "description" => "Роль обычного пользователя",
                ]);

                $this->model->create([
                    "name" => "Комиссия ППМИ",
                    "alias" => "committee_ppmi",
                    "description" => "Комиссия ППМИ",
                ]);
            }

            set_permissions($this->model, $this->output, $this->actions);
        } catch (\Exception $e) {

            $errorMessage = $e->getMessage();
            $this->output->writeln("<error>{$errorMessage}</error> ");
        }
    }
}
