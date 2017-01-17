<?php
namespace App\Model;

use Eloquent, DB;
use Watson\Validating\ValidatingTrait;

Class Log extends Eloquent
{
	use ValidatingTrait;

    protected $table='logs';

    /**
     * Validation rules
     */
 
    protected $rules = array(
        'type'           => 'required|between:1,64',
        'subject'        => 'required|between:1,64',
        'desc'           => 'between:1,255'
    );

	public function setUpdatedAtAttribute($value)
	{
		// Do nothing.
	}

    public function user()
    {
        return $this->belongsTo('User');
    }
}