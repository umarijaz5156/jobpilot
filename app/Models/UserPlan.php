<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Plan\Entities\Plan;

class UserPlan extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     *  Customer scope
     *
     * @return mixed
     */
    public function scopeCompanyData($query, $company_id = null)
    {
        return $query->where('company_id', $company_id ?? currentCompany()->id);
    }

    /**
     *  Company scope for api response
     *
     * @return mixed
     */
    public function scopeApiCompanyData($query, $company_id = null)
    {
        return $query->where('company_id', $company_id ?? auth()->user()->companyId());
    }

    /**
     * Get the company that owns the UserPlan
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(company::class, 'company_id');
    }

    /**
     * Get the plan that owns the UserPlan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
