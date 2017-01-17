<?php

class PlanTableSeeder extends Seeder {

    public function run()
    {
        DB::table('plans')->delete();

        \App\Model\Plan::create(array(
            'reseller_id' => 1,
            'name' => 'Free',
            'sort' => 10,
            'undeletable' => 1,
            'settings' => '{"max_sites":"16","support":"-","domain":"1","download":null,"monthly":"0","annual":"0","currency":"USD","featured":"0"}'
        ));

		\App\Model\Plan::create(array(
            'reseller_id' => 1,
            'name' => 'Standard',
            'sort' => 20,
            'settings' => '{"max_sites":"5","support":"-","domain":"1","download":null,"monthly":"12","annual":"10","currency":"USD","featured":"0"}'
        ));

		\App\Model\Plan::create(array(
            'reseller_id' => 1,
            'name' => 'Deluxe',
            'sort' => 30,
            'settings' => '{"max_sites":"28","support":"-","domain":"1","download":null,"monthly":"24","annual":"20","currency":"USD","featured":"0"}'
        ));

		\App\Model\Plan::create(array(
            'reseller_id' => 1,
            'name' => 'Professional',
            'sort' => 40,
            'settings' => '{"max_sites":"0","support":"-","domain":"1","download":null,"monthly":"36","annual":"32","currency":"USD","featured":"0"}'
        ));

    }
}