<?php

class AssignedRoleTableSeeder extends Seeder {

    public function run()
    {
        DB::table('assigned_roles')->delete();

		$admin = Role::find(1);
		$user = User::find(1);

		$user->attachRole($admin);
    }
}