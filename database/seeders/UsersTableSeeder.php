<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $credentials = array(
            "email" => 'user@thinkingsmart.io',
            "password" => 'MT9yKr5DH*WUSE5-N24;',
            "first_name" => 'Smart',
            "last_name" => 'Loan',
        );
        $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::registerAndActivate($credentials);
        $role = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::findRoleBySlug('admin');
        $role->users()->attach($user);
        //assign user to branch
        $permission = new \App\Models\BranchUser();
        $permission->branch_id = 1;
        $permission->user_id = 2;
        $permission->save();
    }
}
