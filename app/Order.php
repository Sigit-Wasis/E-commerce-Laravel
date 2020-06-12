<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    
    //MEMBUAT RELASI KE MODEL DISTRICT.PHP
    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
