<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationEstimate extends Model
{
    protected $guarded = [];

    public function column()
    {
        return $this->belongsTo(ApplicationScoreColumn::class, 'application_score_column_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getUserNameAttribute()
    {
        return $this->user ? "{$this->user->first_name}" : '---';
    }

    public function getUserPointsAttribute()
    {
        return $this->user ? $this->user : '---';
    }

    public function entity()
    {
        return $this->morphTo();
    }
}
