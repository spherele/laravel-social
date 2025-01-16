<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['text', 'user_id', 'chat_id'];

    // Отношение "один ко многим" с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Отношение "один ко многим" с чатом
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
