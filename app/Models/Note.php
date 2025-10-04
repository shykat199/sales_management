<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'noteable_id',
        'noteable_type',
        'note',
    ];
}
