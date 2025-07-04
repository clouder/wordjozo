<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    public $fillable = [
        'user_id',
        'book_id',
        'number',
        'summary',
    ];
}
