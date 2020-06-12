<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    // Mutator memiliki peran untuk mengubah value sebelum menyimpannya ke dalam database
    public function setPasswordAttribute($value)
	{
	    $this->attributes['password'] = bcrypt($value);
	}
}
