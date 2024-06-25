<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateCvView extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['expired_date', 'view_date_time'];

    protected $casts = [
        'expired_date' => 'datetime',
        'view_date_time' => 'datetime:d/m/y',
    ];

    public function getViewDateTimeAttribute()
    {
        $view_date = $this->view_date;
        if ($view_date) {
            return date('d/m/y', strtotime($view_date));
        }
    }

    public function getExpiredDateAttribute()
    {
        $view_date = $this->view_date;
        $date = Carbon::parse($view_date)->addDays(30);
        if ($date) {
            return date('d/m/y', strtotime($date));
        }
    }
}
