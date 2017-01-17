<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Codesleeve\Stapler\ORM\EloquentTrait;

class User extends Eloquent implements ConfideUserInterface, StaplerableInterface {
    use ConfideUser;
    use HasRole;
    use EloquentTrait;

    protected $table = 'users';

    /**
     * Validation rules
     */

    public static $rules = array(
        'username' => 'required|alpha_dash|between:4,16',
        'email' => 'required|email|between:4,64',
        'password' => 'min:5|max:100',
    );

    /**
     * Laravel-Stapler
     */

    protected $fillable = ['avatar'];

    public function __construct(array $attributes = array()) {
        $this->hasAttachedFile('avatar', [
            'styles' => [
                'medium' => '128x128#',
                'small' => '64x64#'
            ]
        ]);

        parent::__construct($attributes);
    }

    /**
     * Soft delete
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function getDates()
    {
        return array('created_at', 'updated_at', 'last_login', 'expires');
    }

    public function scopeAccount($query)
    {
        if(Auth::check())
        {
            $this->parent_user_id = (Auth::user()->parent_id == NULL) ? Auth::user()->id : Auth::user()->parent_id;
        }
        else
        {
            $this->parent_user_id = NULL;
        }

        return $query->where('id', '=', $this->parent_user_id)
            ->orWhere('parent_id', '=', $this->parent_user_id)
            ->orderBy('parent_id', 'ASC')
            ->orderBy('confirmed', 'DESC')
            ->orderBy('first_name', 'ASC')
            ->orderBy('username', 'ASC');
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function reseller()
    {
        return $this->hasOne('\App\Model\Reseller', 'id', 'reseller_id', 'resellers');
    }

    public function parentUser()
    {
        return $this->belongsTo('\User','parent_id');
    }

    public function childUsers()
    {
        return $this->hasMany('\User','parent_id');
    }

    public function plan()
    {
        return $this->hasOne('\App\Model\Plan', 'id', 'plan_id', 'plans');
    }

    public function campaigns()
    {
        return $this->hasMany('\App\Model\Campaign');
    }

    public function orders()
    {
        return $this->hasMany('App\Model\Order');
    }

    public function roles()
    {
        return $this->belongsToMany('Role', 'assigned_roles');
    }

    public function scopeGetRoles($query)
    {
        $roles = array();

        foreach($this->roles()->get() as $role)
        {
            $roles[] = array(
                'id' => $role->id,
                'name' => trans('global.' . $role->name)
            );
        }
        return $roles;
    }

    public function scopeGetRolesString($query)
    {
        $roles = '';

        foreach($this->roles()->get() as $role)
        {
            $roles .= trans('global.' . $role->name) . ', ';
        }
        $roles = trim($roles, ', ');

        return $roles;
    }

    public function scopeGetRoleId($query)
    {
        $role = $this->roles()->get()->first();

        return $role->id;
    }
}