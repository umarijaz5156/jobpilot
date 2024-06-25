<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $fillable = ['email'];

    protected static function newFactory()
    {
        return \Modules\Newsletter\Database\factories\EmailFactory::new();
    }
}
