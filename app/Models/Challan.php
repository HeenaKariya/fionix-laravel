<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challan extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'user_id',
        'bill_date',
        'amount',
        'payment_type',
        'expense_category',
        'upload_image',
        'note',
        'status',
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
