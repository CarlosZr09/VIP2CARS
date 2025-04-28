<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['name','short_name','min_val','max_val','alphanumeric','status'];

    public function customers()
    {
      return $this->hasMany(Customer::class);
    }


}
