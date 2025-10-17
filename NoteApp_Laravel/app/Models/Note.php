<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Note extends Model
{
     protected $fillable = ['title', 'text', 'user_id', 'status_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
   }
}