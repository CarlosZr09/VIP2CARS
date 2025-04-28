<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name','last_name','document_id','document_number',
        'business_name','email','phone'
      ];

      public function document()
      {
        return $this->belongsTo(Document::class);
      }

      public function getRequiresBusinessNameAttribute()
      {
        return $this->document && $this->document->short_name === 'RUC';
      }
}
