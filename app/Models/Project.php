<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'location', 'start_date', 'status', 'note','budget'];

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_supervisor', 'project_id', 'user_id');
    }
    public function moneyRequests()
    {
        return $this->hasMany(MoneyRequest::class);
    }
    public function challans()
    {
        return $this->hasMany(Challan::class);
    }
}
