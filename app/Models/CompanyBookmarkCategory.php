<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBookmarkCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'name',
    ];

    /**
     * Get the company that owns the CompanyBookmarkCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bookmarks()
    {

        return $this->belongsToMany('bookmark_company_category');
    }
}
