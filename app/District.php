<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
	protected $guarded = [];
	
    //BUAT RELASI KE MODEL CITY.PHP
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
