<?php
namespace App\Model;

use Eloquent;

Class Setting extends Eloquent
{

    protected $table='settings';

	// Disabling Auto Timestamps
    public $timestamps = false;

    public function users()
    {
        return $this->hasOne('User');
    }

}