<?php

namespace Database\Seeders;

use App\Models\ApplicationScoreColumn;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(RoleTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(MunicipalityTableSeeder::class);
//        $this->call(ContestTableSeeder::class);
//        $this->call(RegisterTableSeeder::class);
//        $this->call(PPMIApplicationTableSeeder::class);
//        $this->call(LTOSApplicationTableSeeder::class);
//        $this->call(LPTOSApplicationTableSeeder::class);
//        $this->call(SZPTOSApplicationTableSeeder::class);
//        $this->call(ApplicationScoreColumnsSeeder::class);
//        $this->call(LSApplicationTableSeeder::class);
//        $this->call(MostBeautifulVillageTableSeeder::class);

        // Разрешения которые не относятся к сущностям
//        $this->call(OtherTableSeeder::class);

        Role::where('alias', 'su')->first()?->permissions()->sync(Permission::all()->pluck('id'));

        Role::where('alias', 'user')->first()?->permissions()
            ->sync(Permission::where('group', 'ppmi_application')
            ->orWhere('action', 'show_user')->pluck('id'));

        Role::where('alias', 'committee_ppmi')->first()?->permissions()
            ->sync(Permission::whereIn('group', ['ppmi_application', 'ltos_application', 'lptos_application', 'szptos_application'])
            ->orWhere('action', 'show_committee')->pluck('id'));
    }
}
