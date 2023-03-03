<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $admin_role = new \App\Models\Role();
        $admin_role->name = 'Admin';
        $admin_role->slug = 'admin';
        $admin_role->save();

        $admin_user = new \App\Models\User();
        $admin_user->firstname = 'Brian';
        $admin_user->lastname = 'Liu';
        $admin_user->email = 'brian@vector5.com.au';
        $admin_user->email_verified_at = Carbon::now();
        $admin_user->terms_accepted_at = Carbon::now();
        $admin_user->password = Hash::make('admin');
        $admin_user->save();

        $admin_user->roles()->attach($admin_role);

        $publisher = new \App\Models\User();
        $publisher->firstname = 'Pak9yan';
        $publisher->lastname = 'Liu';
        $publisher->email = 'pakyan@hotmail.com';
        $publisher->email_verified_at = Carbon::now();
        $publisher->terms_accepted_at = Carbon::now();
        $publisher->password = Hash::make('admin');
        $publisher->save();

    }
}
