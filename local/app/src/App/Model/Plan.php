<?php
namespace App\Model;

use Eloquent;

Class Plan extends Eloquent
{

    protected $table='plans';

	// Disabling Auto Timestamps
    public $timestamps = false;

}