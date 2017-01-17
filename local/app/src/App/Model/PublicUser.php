<?php
namespace App\Model;

use Eloquent;

Class PublicUser extends Eloquent
{

    protected $table='public_users';

	public function getDates()
	{
		return array('created_at', 'updated_at', 'last_login');
	}

    public function user()
    {
        return $this->hasOne('User');
    }

    public function app()
    {
        return $this->hasOne('\Mobile\Model\App', 'id', 'app_id');
    }

}