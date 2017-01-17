<?php

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        $users = array(
            'reseller_id' => 1,
            'reseller' => 1,
            'plan_id' => 4,
            'first_name' => 'System',
            'last_name' => 'Owner',
            'username' => 'admin',
            'email' => 'info@example.com',
            'password' => Hash::make('welcome'),
            'confirmation_code' => 'seeded',
            'confirmed' => 1,
            'created_at' => date('Y-m-d H:i:s')
        );
		DB::table('users')->insert($users);
    }
}