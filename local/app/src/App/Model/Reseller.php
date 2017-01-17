<?php
namespace App\Model;

use Eloquent;

Class Reseller extends Eloquent
{

    protected $table='resellers';

    public function users()
    {
        return $this->hasMany('User');
    }

    public function orders()
    {
        return $this->hasMany('App\Model\Order');
    }
}