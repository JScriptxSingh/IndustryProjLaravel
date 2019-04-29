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
        $role_employee  = Role::where('name', 'employee')->first();
        $role_manager   = Role::where('name', 'manager')->first();
    
        // 
        $employee               = new User();
        $employee->firstName    = 'Employee';
        $employee->lastName     = 'Emp';
        $employee->email        = 'employee@home.com';
        $employee->password     = bcrypt('P@ssw0rd!');
        $employee->save();
        $employee->roles()->attach($role_employee);
    
        $manager                = new User();
        $manager->firstName     = 'Manager';
        $manager->lastName      = 'Man';
        $manager->email         = 'manager@home.com';
        $manager->password      = bcrypt('P@ssw0rd!');
        $manager->save();
        $manager->roles()->attach($role_manager);
    }
}
