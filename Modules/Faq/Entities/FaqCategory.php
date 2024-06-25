<?php

namespace Modules\Faq\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\Faq\Database\factories\FaqcategoryFactory::new();
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }
}
