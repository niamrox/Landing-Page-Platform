<?php
namespace Lead\Model;

use Eloquent, DB;

Class LeadIndustry extends \LaravelBook\Ardent\Ardent
{

    protected $table='lead_industries';

    public $timestamps = false;

    /**
     * Validation rules
     */
 
    public static $rules = array(
        'name'    => 'required|between:1,64'
    );

}