<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommitteeAuthorization extends Model
{
    //
    protected $fillable = [
        'user_id',
        'committee_id',
        'granted_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function granter()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
