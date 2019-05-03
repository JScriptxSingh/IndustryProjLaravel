<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get roles.
        $role_admin   = Role::where('name', 'admin')->first();
    
        $admin                = new User();
        $admin->firstName     = 'Admin';
        $admin->lastName      = 'User';
        $admin->email         = 'admin@numinix.com';
        $admin->password      = bcrypt('xGPc8x+G');
        $admin->save();
        $admin->roles()->attach($role_admin);
    }
}
