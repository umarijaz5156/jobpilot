<?php

namespace Modules\SetupGuide\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetupGuide extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\SetupGuide\Database\factories\SetupGuideFactory::new();
    }
}
