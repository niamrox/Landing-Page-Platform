<?php

class PermissionTableSeeder extends Seeder {

    public function run()
    {
        DB::table('permissions')->delete();

		// Permissions
        $system_management = Permission::create(array(
            'name' => 'system_management',
            'display_name' => 'System Management'
        ));

        $user_management = Permission::create(array(
            'name' => 'user_management',
            'display_name' => 'User Management'
        ));

        $site_management = Permission::create(array(
            'name' => 'site_management',
            'display_name' => 'Site Management'
        ));

		// Reseller
		$owner = Role::find(1);
		$owner->perms()->sync(array(
            $system_management->id, 
            $user_management->id,
            $site_management->id
        ));

		// Admin
		$admin = Role::find(2);
		$admin->perms()->sync(array(
            $user_management->id,
            $site_management->id
        ));

		// Site User
		$manager = Role::find(3);
		$manager->perms()->sync(array(
            $site_management->id
        ));
    }
}