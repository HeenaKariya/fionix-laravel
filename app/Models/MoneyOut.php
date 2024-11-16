<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyOut extends Model
{
    use HasFactory;
    protected $table = 'money_out';

    protected $fillable = [
        'project_id',
        'user_id',
        'transaction_id',
        'from',
        'to',
        'payment_type',
        'payment_date',
        'amount',
        'image',
        'notes',
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
