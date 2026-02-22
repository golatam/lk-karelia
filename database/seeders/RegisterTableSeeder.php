<?php
namespace Database\Seeders;

use App\Models\Register;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;

class RegisterTableSeeder extends Seeder
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

            $this->model = (new Register());

            $this->output = new ConsoleOutput();

            if (($handle = fopen(public_path("import/membership.csv"), 'r' )) !== FALSE) {

                $i=0;$key=0;

                $fp = file(public_path("import/membership.csv"), FILE_SKIP_EMPTY_LINES);

                $progress = $this->command->getOutput()->createProgressBar(count($fp));

                while(($string = fgetcsv($handle, 1000, Chr(39))) !== FALSE) {

                    $data = Str::of($this->trimall($string[0]))->explode(Chr(59));
                    $nameAccordingCharter = isset($data[5]) && !empty($this->trimall($data[5])) ? $this->trimall($data[5]) : null;  // Наименование (согласно уставу)
                    $membership = isset($data[6]) && !empty($this->trimall($data[6])) ? $this->trimall($data[6]) : null;            // Членство в АР ТОС РК
                    if (!in_array($key, [0, 1, 2]) && (!!$nameAccordingCharter && !!$membership)) {

                        DB::table('registers')->where('name_according_charter', $nameAccordingCharter)->update(['membership' => in_array($membership, ['Да', 'да']) ? 1 : 0]);
                        $i++;
                    }

                    $key++;
                    $progress->advance();
                }

                $progress->finish();
                $this->command->getOutput()->newLine();

                fclose ($handle);

                $this->command->line("<fg=cyan>Добавлено элементов реестра ТОС - {$i} шт.</>");
            }

            set_permissions($this->model, $this->output, $this->actions);
        } catch (\Exception $e) {

            $errorMessage = $e->getMessage();
            $this->output->writeln("<error>{$errorMessage}</error> ");
        }
    }

    public function trimall($line){
        $line = preg_replace('/ +/', ' ', $line);
        $line = preg_replace('/\r/', '', $line);
        $line = preg_replace('/\n/', '', $line);
        $line = preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $line);
        $line = str_replace('﻿', '', $line);
        $line = str_replace('\xD0', '', $line);
        $line = str_replace('�', '', $line);
        return trim($line);
    }
}
