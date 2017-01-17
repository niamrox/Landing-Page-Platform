<?php

use Zizaco\Confide\UserValidator as ConfideUserValidator;
use Zizaco\Confide\UserValidatorInterface;

class UserValidator extends ConfideUserValidator implements UserValidatorInterface
{
    public $rules = [
        'create' => [
            'username' => 'required|alpha_dash|between:4,16',
            'email'    => 'required|email|between:4,64',
            'password' => 'required|between:5,100',
        ],
        'update' => [
            'username' => 'required|alpha_dash|between:4,16',
            'email'    => 'required|email|between:3,64',
            'password' => 'min:5|max:100',
        ]
    ];
}