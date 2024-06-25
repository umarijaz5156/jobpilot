<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Plan\Entities\Plan;

class Earning extends Model
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
     * Get the company that owns the Earning
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get the plan that owns the Earning
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Get the manualPayment that owns the Earning
     */
    public function manualPayment(): BelongsTo
    {
        return $this->belongsTo(ManualPayment::class, 'manual_payment_id');
    }
}
