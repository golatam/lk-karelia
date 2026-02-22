<?php
namespace Database\Seeders;

use App\Models\LSApplication;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Output\ConsoleOutput;

class LSApplicationTableSeeder extends Seeder
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

            $this->model = (new LSApplication());

            $this->output = new ConsoleOutput();

            set_permissions($this->model, $this->output, $this->actions);
        } catch (\Exception $e) {

            $errorMessage = $e->getMessage();
            $this->output->writeln("<error>{$errorMessage}</error> ");
        }
    }
}
