<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    const ROLE_CUSTOMER = 'customer';
    const ROLE_RESTAURANT_OWNER = 'restaurant_owner';
    const ROLE_DELIVERY_PERSON = 'delivery_person';
    const ROLE_ADMIN = 'admin';

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // Additional checks for other roles can be added
}
