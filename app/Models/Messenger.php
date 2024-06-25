<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Messenger extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'from',
        'to',
        'body',
        'read',
        'messenger_user_id',
    ];

    protected $appends = ['created_time'];

    public function getCreatedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'to');
    }

    public function scopeCandidateMessages($query, $user)
    {
        return $query->select('id', 'from', 'to', 'body', 'created_at')->where(function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('from', auth()->id());
                $q->where('to', $user->candidate->user_id);
            })->orWhere(function ($q) use ($user) {
                $q->where('to', auth()->id());
                $q->where('from', $user->candidate->user_id);
            });
        });
    }

    public function scopeCompanyMessages($query, $user)
    {
        return $query->select('id', 'from', 'to', 'body', 'created_at')->where(function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('from', auth()->id());
                $q->where('to', $user->company->user_id);
            })->orWhere(function ($q) use ($user) {
                $q->where('to', auth()->id());
                $q->where('from', $user->company->user_id);
            });
        });
    }
}
