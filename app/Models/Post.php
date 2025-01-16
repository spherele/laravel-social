<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $fillable = [
        'content',
        'file_type',
        'file_url',
        'likes',
        'comments',
        'watches',
    ];
}
