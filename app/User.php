<?php

namespace App;
use Backpack\CRUD\CrudTrait;  
use Spatie\Permission\Traits\HasRoles; 
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword ;
class User extends Authenticatable implements CanResetPassword
{
    use Notifiable;
    use CrudTrait;  
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
