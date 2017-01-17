<?php
namespace Lead\Model;

use Eloquent, DB;

Class Lead extends Eloquent
{
    protected $table='leads';

	/**
	 * Soft delete
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at', 'created_at', 'updated_at'];

	public function getDates()
	{
		return array('created_at', 'updated_at');
	}

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function site()
    {
        return $this->belongsTo('Web\Model\Site', 'site_id', 'id');
    }
}