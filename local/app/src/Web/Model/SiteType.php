<?php
namespace Web\Model;

use Eloquent, DB;

Class SiteType extends Eloquent
{

    protected $table='site_types';

    public $timestamps = false;

    public function site()
    {
        return $this->hasOne('Web\Model\Site');
    }
}