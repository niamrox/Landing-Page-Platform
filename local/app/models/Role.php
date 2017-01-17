<?php

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
	public function user()
	{
		return $this->belongsTo('\User');
	}

	public function scopeRoles($query)
	{
        foreach($query->where('id', '>', 1)->where('id', '<', 5)->get() as $role)
        {
            $name = (\Lang::has('global.' . $role->name)) ? trans('global.' . $role->name) : $role->name;
            $roles[$role->id] = $name;
        }

		return $roles;
	}

	public function scopeRolesAdmin($query)
	{
        foreach($query->where('id', '<', 3)->get() as $role)
        {
            $name = (\Lang::has('global.' . $role->name)) ? trans('global.' . $role->name) : $role->name;
            $roles[$role->id] = $name;
        }

		return $roles;
	}
}