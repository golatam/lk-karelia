<?php

namespace App\Console\Commands;

use App\Models\Municipality;
use App\Models\Register;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportFromCsvToDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv {--municipalities} {--register}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('municipalities')) {

            $this->municipalities();

            return 1;
//        } elseif ($this->option('users')) {
//
////            $this->users();
//
//            return;
        } elseif ($this->option('register')) {

            $this->register();

            return 1;
        } else {

            $this->all();

            return 1;
        }
    }

    public function all()
    {
        $this->municipalities();
        $this->register();
//        $this->users();
    }

    public function municipalities($i0 = 0, $i1 = 0, $i2 = 0)
    {
        try {

            $level0 = collect();
            $level1 = collect();
            $level2 = collect();

            $models = collect();

            if (($handle = fopen(public_path("/mo.csv"), 'r' )) !== FALSE) {

                while(($string = fgetcsv($handle, 1000, Chr(9))) !== FALSE) {

                    $data = Str::of($this->trimall($string[0]))->explode(',');

                    if ((string) $data->get(0) !== 'MR') {

                        $level0[] = $data->get(0);
                        $level1[] = $data->get(1);
                        $level2[] = $data->get(2);
                        $models[] = $data;
                    }
                }

                fclose ($handle);
            }

            $progress = $this->output->createProgressBar($level0->count());

            foreach ($level0 as $item) {

                $model = Municipality::firstOrNew([
                    'name' => trim($item),
                ]);

                if (!$model->exists) {

                    $model->save();

                    $i0++;
                }

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<info>Добавлено {$i0} муниципалитетов 0 уровня из {$level0->count()}</info>");

            $this->output->newLine();

            $parentMunicipalities = Municipality::whereNull('parent_id')->get();

            $progress = $this->output->createProgressBar($level1->count());

            foreach ($level1 as $item) {

                $level0 = $models->where(1, $item)->first();

                $parentMunicipality = $parentMunicipalities->where('name', $level0->get(0))->first();

                if ($parentMunicipality) {

                    $model = Municipality::firstOrNew([
                        'parent_id' => $parentMunicipality->id,
                        'name' => trim($item),
                    ]);

                    if (!$model->exists) {

                        $model->save();

                        $i1++;
                    }
                }

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<info>Добавлено {$i1} муниципалитетов 1 уровня из {$level1->count()}</info>");

            $this->output->newLine();

            $parentMunicipalities = Municipality::whereNotNull('parent_id')->get();

            $progress = $this->output->createProgressBar($level1->count());

            foreach ($level2 as $item) {

                $level1 = $models->where(2, $item)->first();

                $parentMunicipality = $parentMunicipalities->where('name', $level1->get(1))->first();

                if ($parentMunicipality) {

                    $model = Municipality::firstOrNew([
                        'parent_id' => $parentMunicipality->id,
                        'name' => trim($item),
                    ]);

                    if (!$model->exists) {

                        $model->save();

                        $i2++;
                    }
                }

                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();

            $this->line("<info>Добавлено {$i2} муниципалитетов 2 уровня из {$level2->count()}</info>");
        } catch (\Exception $e) {

            $this->line("<error>Код ответа: {$e->getMessage()}</error>");
        }
    }

    public function users($i = 0)
    {
        try {

            if (($handle = fopen(public_path("/users.csv"), 'r' )) !== FALSE) {

                while(($string = fgetcsv($handle, 1000, Chr(9))) !== FALSE) {

                    $data = Str::of($this->trimall($string[0]))->explode(';');

                    $modelMunicipality = Municipality::firstOrNew([
                        'name' => trim($data->get(0)),
                    ]);

                    if ($modelMunicipality->exists) {

                        $name = (string) Str::of($data->get(1))->beforeLast('@');

                        $modelUser = User::firstOrNew([
                            'name' => $name,
                            'email' => trim($data->get(1)),
                            'municipality_id' => $modelMunicipality->id,
                        ]);

                        if (!$modelUser->exists) {

                            $modelUser->password = Hash::make('12345678');
                            $modelUser->save();

                            $i++;
                        }
                    } else {

                        $name = (string) Str::of($data->get(1))->beforeLast('@');

                        $modelUser = User::firstOrNew([
                            'name' => $name,
                            'email' => trim($data->get(1)),
                            'municipality_id' => 0,
                        ]);

                        if (!$modelUser->exists) {

                            $modelUser->password = Hash::make('12345678');
                            $modelUser->save();

                            $i++;
                        }
                    }


                    $role = Role::where('slug', 'user')->where('name', 'user')->first();

                    $modelUser->roles()->sync($role ? [$role->id] : []);
                }

                fclose ($handle);
            }

            $this->line("<info>Добавлено пользователей - {$i} шт.</info>");
        } catch (\Exception $e) {

            $this->line("<error>Код ответа: {$e->getMessage()}</error>");
        }
    }

    public function register($i = 0, $key = 0)
    {
        try {

            if (($handle = fopen(public_path("/tos.csv"), 'r' )) !== FALSE) {

                $fp = file(public_path("/tos.csv"), FILE_SKIP_EMPTY_LINES);

                DB::table('registers')->delete();
                DB::statement("ALTER TABLE `registers` AUTO_INCREMENT = 1;");

                $progress = $this->output->createProgressBar(count($fp));

                while(($data = fgetcsv($handle, 0, Chr(59))) !== FALSE) {

                    if ($key > 1 && (!empty($this->trimall($data[2])) && !empty($this->trimall($data[3])) && !empty($this->trimall($data[4])))) {

                        $municipalityNameRegion = Municipality::where('name', $this->trimall($data[2]))->first();
                        $municipalityNameSettlement = Municipality::where('name', $this->trimall($data[3]))->first();

                        $dataModel = [
                            'name_region' => $municipalityNameRegion ? $municipalityNameRegion->id : 0,                      // Наименование муниципального района / городского округа
                            'name_settlement' => $municipalityNameSettlement ? $municipalityNameSettlement->id : 0,                  // Наименование поселения в составе района
                            'name_according_charter' => $this->trimall($data[4]),           // Наименование (согласно уставу)
                            'is_legal_entity' => $this->trimall($data[5]) === 'нет' ? 0 : 1,// Является ли ТОС юридическим лицом (да/нет)
                            'address' => $this->trimall($data[6]),                          // Адрес местонахождения ТОС (для юридических лиц - юридический адрес)
                            'boundaries' => $this->trimall($data[7]),                       // Границы ТОС
                            'legal_act' => $this->trimall($data[8]),                        // Муниципальный правовой акт об утверждении устава ТОС (вид документа, дата, номер)
                            'registration_date_charter' => Carbon::parse(str_replace('/', '.', $this->trimall($data[9]))),    // Дата регистрации
                            'number_members' => (int) $this->trimall($data[10]),            // Кол-во членов ТОС
                            'number_citizens' => (int) $this->trimall($data[11]),           // Кол-во граждан, проживающих в границах ТОС
                            'fio_chief' => $this->trimall($data[12]),                       // ФИО руководителя ТОС
                            'email_chief' => $this->trimall($data[13]),                     // Электронный адрес руководителя ТОС
                            'phone_chief' => $this->trimall($data[14]),                     // Мобильный телефон руководителя ТОС
                            'note' => $this->trimall($data[15]),                            // Примечание
                        ];

                        $registerTos = Register::create($dataModel);

                        if (!!$registerTos) {
                            $this->output->newLine();
                            $this->line("<fg=cyan>{$registerTos->name_region}, {$registerTos->name_settlement}, {$registerTos->name_according_charter}</>");

                            $i++;
                        } else {

                            $this->output->newLine();
                            $this->line("<fg=black;bg=yellow>{$dataModel['name_region']} {$dataModel['name_settlement']} {$dataModel['name_according_charter']}</>");
                        }
                    }

                    $key++;

                    $progress->advance();
                }

                $progress->finish();
                $this->output->newLine();

                fclose ($handle);
            }

            $this->line("<fg=cyan>Добавлено элементов реестра ТОС - {$i} шт.</>");
        } catch (\Exception $e) {

            $this->line("<fg=black;bg=yellow>Код ответа: {$e->getMessage()}</>");
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
