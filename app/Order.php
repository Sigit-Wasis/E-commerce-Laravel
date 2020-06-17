<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    //  Ingat, Accessor dimulai dengan key get dan diakhiri dengan key attribute.
    protected $appends = ['status_label'];

	public function getStatusLabelAttribute()
	{
	    if ($this->status == 0) {
	        return '<span class="badge badge-secondary">Baru</span>';
	    } elseif ($this->status == 1) {
	        return '<span class="badge badge-primary">Dikonfirmasi</span>';
	    } elseif ($this->status == 2) {
	        return '<span class="badge badge-info">Proses</span>';
	    } elseif ($this->status == 3) {
	        return '<span class="badge badge-warning">Dikirim</span>';
	    }
	    return '<span class="badge badge-success">Selesai</span>';
	}
    
    //MEMBUAT RELASI KE MODEL DISTRICT.PHP
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    //MEMBUAT RELASI KE TABLE ORDER_DETAILS DENGAN JENIS RELASI ONE TO MANY
	public function details()
	{
	    return $this->hasMany(OrderDetail::class);
	}

	// Bagian terakhir adalah membuat relasi dari orders ke payments, buka file Order.php
	public function payment()
	{
	    return $this->hasOne(Payment::class);
	}

	public function customer()
	{
	    return $this->belongsTo(Customer::class);
	}

	public function return()
	{
	    return $this->hasOne(OrderReturn::class);
	}
}
