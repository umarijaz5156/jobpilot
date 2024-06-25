<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     *  Reset password scope
     *
     * @return mixed
     */
    public function scopeReset($query)
    {
        return $query->where('type', 'reset_password');
    }

    /**
     *  Email verify scope
     *
     * @return mixed
     */
    public function scopeVerify($query)
    {
        return $query->where('type', 'email_verify');
    }
}
