<?php
namespace Database\Seeders;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Output\ConsoleOutput;

use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    protected $actions = [
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

            $this->model = (new User());

            $this->output = new ConsoleOutput();

            if (config('app.common.is_set_data_seed')) {

                $su = Role::find(1);

                $model = $this->model->firstOrNew(["email" => "admin@example.com"]);

                if (!$model->exists) {
                    $model
                        ->fill([
                            "first_name" => "Супер пользователь",
                            "email" => "admin@example.com",
                            "password" => Hash::make('secret'),
                            "role_id" => $su ? $su->id : 1,
                            "is_active" => 1,
                        ])
                        ->save();
                }
            }

            set_permissions($this->model, $this->output, $this->actions);
        } catch (\Exception $e) {

            $errorMessage = $e->getMessage();
            $this->output->writeln("<error>{$errorMessage}</error> ");
        }
    }
}
