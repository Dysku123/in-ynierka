<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable =[
       'user_id',
       'label',
       'name',
       'surname',
       'phone',
       'street',
       'postal_code',
       'city',
       'is_default'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
