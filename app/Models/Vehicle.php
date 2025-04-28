<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'plate',
        'brand',
        'model',
        'year_of_manufacture',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->brand} {$this->model} ({$this->plate})";
    }
}
