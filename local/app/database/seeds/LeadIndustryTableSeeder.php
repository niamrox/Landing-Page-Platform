<?php

class LeadIndustryTableSeeder extends Seeder {

    public function run()
    {
        DB::table('lead_industries')->delete();

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'other'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'accounting_finance'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'alternative_health_and_wellness'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'art'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'author'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'beauty_appearance'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'business_consulting_advice' // Business Consulting/Advice
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'career_job_advice' // Career/Job Advice
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'coaches_consultants'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'dating_relationships'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'debt_consolidation'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'education'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'event_planning'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'environmental'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'food_wine'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'financial_investment_publishing' // Financial/Investment Publishing
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'gardening_outdoor_advice' // Gardening/Outdoor Advice
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'health_and_fitness'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'legal'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'lifestyle_advice'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'marketing'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'medical'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'military_veterans'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'music'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'non_profit' // Non-Profit
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'parenting_advice'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'pets'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'real_estate'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'religious'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'retail'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'security_survival'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'social_media'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'skilled_trades_crafts_hobbies' // Skilled trades/crafts/hobbies
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'software_technology'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'sports'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'travel'
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'web_design_information' // Web design/information
        ));

        \Lead\Model\LeadIndustry::create(array(
            'name' => 'web_traffic'
        ));

    }
}