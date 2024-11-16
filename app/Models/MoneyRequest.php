<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'user_id',
        'date',
        'amount',
        'payment_type',
        'note',
        'status',
        'admin_status',
        'admin_note',
        'manager_status',
        'manager_note',
        'amanager_status',
        'amanager_note',
        'admin_status_updated_at',
        'manager_status_updated_at',
        'amanager_status_updated_at',
        'admin_id',
        'manager_id',
        'amanager_id',
        
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function amanager()
    {
        return $this->belongsTo(User::class, 'amanager_id');
    }
}
