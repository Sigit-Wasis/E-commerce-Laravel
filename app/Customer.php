<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
	use Notifiable;
    protected $guarded = [];

    // Mutator memiliki peran untuk mengubah value sebelum menyimpannya ke dalam database
    public function setPasswordAttribute($value)
	{
	    $this->attributes['password'] = bcrypt($value);
	}
}
