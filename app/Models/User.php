<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\MoneyRequest;
use App\Models\Challan;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile_no',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_supervisor', 'user_id', 'project_id');
    }
    public function role()
    {
        $role = Role::find($this->role_id);

        return $role ? $role->name : null;
    }
    public function calculateBalance()
    {
        $approvedRequests = MoneyRequest::where('user_id', $this->id)
                                        ->where('amanager_status', 'approved')
                                        ->sum('amount') ?? 0;

        $approvedChallans = Challan::where('user_id', $this->id)
                                    ->where('status', 'approved')
                                    ->sum('amount') ?? 0;

        return $approvedRequests - $approvedChallans;
    }
    

    public function calculateProjectBalance($projectId)
    {
        $approvedRequestsProjectwise = MoneyRequest::where('user_id', $this->id)
                                        ->where('project_id', $projectId)
                                        ->where('amanager_status', 'approved')
                                        ->sum('amount') ?? 0;

        $approvedChallansProjectwise = Challan::where('user_id', $this->id)
                                    ->where('project_id', $projectId)
                                    ->where('status', 'approved')
                                    ->sum('amount') ?? 0;

        return $approvedRequestsProjectwise - $approvedChallansProjectwise;
    }

    public function calculateBalanceTotal()
    {
        $approvedRequests = MoneyRequest::where('user_id', $this->id)
                                        ->where('amanager_status', 'approved')
                                        ->sum('amount') ?? 0;

        // $approvedChallans = Challan::where('user_id', $this->id)
        //                             ->where('status', 'approved')
        //                             ->sum('amount') ?? 0;
        $approvedChallans=0;
        return $approvedRequests - $approvedChallans;
    }

    public function calculateProjectBalanceTotal($projectId)
    {
        $approvedRequestsProjectwise = MoneyRequest::where('user_id', $this->id)
                                        ->where('project_id', $projectId)
                                        ->where('amanager_status', 'approved')
                                        ->sum('amount') ?? 0;

        // $approvedChallansProjectwise = Challan::where('user_id', $this->id)
        //                             ->where('project_id', $projectId)
        //                             ->where('status', 'approved')
        //                             ->sum('amount') ?? 0;
        $approvedChallansProjectwise=0;
        return $approvedRequestsProjectwise - $approvedChallansProjectwise;
    }
    
    public function moneyRequestProject($userId)
    {
        $approvedRequestsforoneproject = MoneyRequest::where('user_id', $userId)
            ->where('amanager_status', 'approved')
            ->sum('amount') ?? 0;


        return $approvedRequestsforoneproject;
    }
    public function moneyRequestProjectnew($userId, $projectId)
    {
        $approvedRequestsProjectwise = MoneyRequest::where('user_id', $this->id)
                                        ->where('project_id', $projectId)
                                        ->where('amanager_status', 'approved')
                                        ->sum('amount') ?? 0;

        $approvedChallansProjectwise = Challan::where('user_id', $this->id)
                                    ->where('project_id', $projectId)
                                    ->where('status', 'approved')
                                    ->sum('amount') ?? 0;

        return $approvedRequestsProjectwise - $approvedChallansProjectwise;
    }

    public function calculateBalanceUserwise($userId)
    {
        $approvedRequests = MoneyRequest::where('user_id', $userId)
            ->where('amanager_status', 'approved')
            ->sum('amount') ?? 0;

        $approvedChallans = Challan::where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('amount') ?? 0;

        return $approvedRequests - $approvedChallans;
    }
    public function calculateProjectBalanceNew($projectId)
    {
        $approvedRequestsProjectwise = MoneyRequest::where('user_id', $this->id)
                                        ->where('project_id', $projectId)
                                        ->where('amanager_status', 'approved')
                                        ->sum('amount') ?? 0;

        $approvedChallansProjectwise = Challan::where('user_id', $this->id)
                                    ->where('project_id', $projectId)
                                    ->where('status', 'approved')
                                    ->sum('amount') ?? 0;

       
        return $approvedRequestsProjectwise - $approvedChallansProjectwise;
    }

    public function calculateProjectBalanceNewOwner($projectId)
    {
        $approvedRequestsProjectwise = MoneyRequest::where('user_id', $this->id)
                                        ->where('project_id', $projectId)
                                        ->where('amanager_status', 'approved')
                                        ->sum('amount') ?? 0;

        $approvedChallansProjectwise = Challan::where('user_id', $this->id)
                                    ->where('project_id', $projectId)
                                    ->where('status', 'approved')
                                    ->sum('amount') ?? 0;

        $projectBalance = $approvedRequestsProjectwise - $approvedChallansProjectwise;

        return [
            'approvedRequests' => $approvedRequestsProjectwise,
            'approvedChallans' => $approvedChallansProjectwise,
            'projectBalance' => $projectBalance
        ];
    }

    public function calculateBalanceUserwiseNew($userId)
    {
        $approvedRequests = MoneyRequest::where('user_id', $userId)
            ->where('amanager_status', 'approved')
            ->sum('amount') ?? 0;

        $approvedChallans = Challan::where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('amount') ?? 0;

       
        return $approvedRequests - $approvedChallans;
    }

    public function allPendingChallan($userId){
        return Challan::where('user_id', $userId)
            ->where('status', 'pending')
            ->sum('amount') ?? 0;
    }

    public function pendingthisProjectChallan($projectId, $userId){
        return Challan::where('user_id', $userId)
                                    ->where('project_id', $projectId)
                                    ->where('status', 'pending')
                                    ->sum('amount') ?? 0;
    }
    
}
