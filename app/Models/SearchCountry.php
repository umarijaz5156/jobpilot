<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchCountry extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function states()
    {
        return $this->hasMany(State::class);
    }
}
