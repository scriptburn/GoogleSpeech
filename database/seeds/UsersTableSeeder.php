<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 2,
                'name' => 'user',
                'email' => 'user@user.com',
                'password' => '$2y$10$AOIqafbesFGnzW8POeyAR.RQZzTtd8aR0CQxsifsYttNZnS5TaynO',
                'remember_token' => 'EXTzweIUigCnsLl8gjuptAzjxnEEUyeGlDGHLKwZyCdymQgrxTwb1yFRozNr',
                'created_at' => '2018-04-25 13:08:36',
                'updated_at' => '2018-04-25 13:33:08',
            ),
            1 => 
            array (
                'id' => 4,
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'password' => '$2y$10$9AoamD4.b1R3HR/pu7hdbuop04D53UBfy6EoknxFadyWa118OtXN2',
                'remember_token' => '6cYe3PyKDPAravjRHL6ewjWKOsHCr4gsaFd7NDsj7gdsQVg5WOHDoEOA1Fi7',
                'created_at' => '2018-04-25 13:12:46',
                'updated_at' => '2018-05-02 06:09:42',
            ),
        ));
        
        
    }
}