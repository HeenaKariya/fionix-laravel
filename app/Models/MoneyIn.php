<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyIn extends Model
{
    use HasFactory;
    protected $table = 'money_in';
    protected $fillable = [
        'project_id',
        'user_id',
        'from',
        'to',
        'payment_type',
        'payment_datetime',
        'amount',
        'notes',
        'transaction_id'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
