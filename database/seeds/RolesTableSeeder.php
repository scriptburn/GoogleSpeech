<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Admin',
                'created_at' => '2018-04-25 12:21:42',
                'updated_at' => '2018-04-25 12:21:42',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'User',
                'created_at' => '2018-04-25 12:25:41',
                'updated_at' => '2018-04-25 12:25:41',
            ),
        ));
        
        
    }
}