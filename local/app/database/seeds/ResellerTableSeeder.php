<?php

class ResellerTableSeeder extends Seeder {

    public function run()
    {
        DB::table('resellers')->delete();

		\App\Model\Reseller::create(array(
			'domain' => '',
			'mail_from_address' => NULL,
			'mail_from_name' => NULL,
			'contact_business' => NULL,
			'contact_name' => NULL,
			'contact_mail' => NULL,
			'contact_address1' => NULL,
			'contact_zip' => NULL,
			'contact_city' => NULL,
			'contact_region' => NULL,
			'contact_country' => NULL
		));
    }
}