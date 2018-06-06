<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Add',
                'created_at' => '2018-04-25 12:19:47',
                'updated_at' => '2018-04-25 12:19:47',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Edit',
                'created_at' => '2018-04-25 12:19:51',
                'updated_at' => '2018-04-25 12:19:51',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Delete',
                'created_at' => '2018-04-25 12:19:57',
                'updated_at' => '2018-04-25 12:19:57',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'View',
                'created_at' => '2018-04-25 12:20:11',
                'updated_at' => '2018-04-25 12:20:11',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Edit Others',
                'created_at' => '2018-04-25 12:20:25',
                'updated_at' => '2018-04-25 12:20:25',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Delete Others',
                'created_at' => '2018-04-25 12:20:32',
                'updated_at' => '2018-04-25 12:20:32',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'View Others',
                'created_at' => '2018-04-25 12:20:38',
                'updated_at' => '2018-04-25 12:20:38',
            ),
        ));
        
        
    }
}