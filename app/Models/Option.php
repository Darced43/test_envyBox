<?php

namespace App\Models;

use App\Models\Poll;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Option extends Model {
    protected $fillable = ['poll_id', 'text'];
    public function poll() { return $this->belongsTo(Poll::class); }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}

