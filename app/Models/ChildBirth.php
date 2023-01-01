<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ChildBirth extends Model
{
    protected $fillable = [
        'mother_id',
        'mother_age',
        'gestational_age',
        'baby_gender',
        'baby_weight',
        'baby_length',
        'birth_description',
        'birthing_method',
        'created_at',
        'updated_at'
    ];
}
