<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\MoneyRequest;
use App\Models\MoneyIn;
use App\Models\MoneyOut;
use App\Models\Challan;
// use Carbon\Carbon;
class AccountManagerController extends Controller
{
    public function activeProjects(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $projects = Project::where('status', '!=', 'finished')->with('supervisors')->get();
        return view('account_manager.active_projects', compact('projects', 'startDate', 'endDate'));
    }
    public function finishedProjects(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $projects = Project::where('status', 'finished')->with('supervisors')->get();
        return view('account_manager.finished_projects', compact('projects', 'startDate', 'endDate'));
    }
    public function show(Project $project)
    {
        $user = Auth::user();
        
        $project->supervisors->contains($user);
        $projectId = $project->id;

        $users = $project->supervisors;
        $userBalances = $users->map(function ($user) use ($projectId) {
            // Total Money Requested (Approved)
            $approvedRequests = MoneyRequest::where('user_id', $user->id)
                ->where('project_id', $projectId)
                ->where('amanager_status', 'approved')
                ->sum('amount') ?? 0;
    
            // Total Expenses (Approved)
            $approvedExpenses = Challan::where('user_id', $user->id)
                ->where('project_id', $projectId)
                ->where('status', 'approved')
                ->sum('amount') ?? 0;
    
            // Current Balance (Total Money Requested - Total Expenses)
            $currentBalance = $approvedRequests - $approvedExpenses;
    
            // Return array containing all the necessary data for each user
            return [
                'user' => $user,
                'approvedRequests' => $approvedRequests,
                'approvedExpenses' => $approvedExpenses,
                'currentBalance' => $currentBalance,
            ];
        });

         // Total Money Out
        $moneyOutSum = MoneyOut::where('project_id', $projectId)
        ->sum('amount') ?? 0;

        // Total Challan
        $moneyRequestSum = MoneyRequest::where('project_id', $projectId)
            ->where('amanager_status', 'approved')
            ->sum('amount') ?? 0;
            
        // Total Money In
        $totalMoneyIn = MoneyIn::where('project_id', $projectId)
        ->sum('amount') ?? 0;

        // Total Money Out
        $approvedChallansProjectwise = $moneyOutSum + $moneyRequestSum;

        // Expense Bill Pending
        $pendingChallansProjectwise = Challan::where('project_id', $projectId)
        ->where('status', 'pending')
        ->sum('amount') ?? 0;
         // Fetch Money In transactions for this project
        $moneyInTransactions = MoneyIn::where('project_id', $projectId)
        ->orderBy('payment_datetime', 'desc')
        ->get();
        // Fetch Money Out Transactions
        $moneyOutTransactions = MoneyOut::where('project_id', $projectId)
        ->orderBy('payment_date', 'desc')
        ->get();
        // Fetch Money Requests where amanger_status is approved
        $approvedMoneyRequests = MoneyRequest::where('project_id', $projectId)
        ->where('amanager_status', 'approved')
        ->get();
        // Fetch Approved Challans
        $approvedChallans = Challan::where('project_id', $projectId)
        ->where('status', 'approved')
        ->get();
        // Fetch Pending Challans
        $pendingChallans = Challan::where('project_id', $projectId)
        ->where('status', 'pending')
        ->with('user') 
        ->get();

        $netProfitLoss =  $totalMoneyIn - $approvedChallansProjectwise; 

        return view('projects.show', compact('project', 'userBalances', 'totalMoneyIn', 'approvedChallansProjectwise', 'pendingChallansProjectwise', 'moneyInTransactions', 'moneyOutTransactions', 'pendingChallans','approvedChallans','approvedMoneyRequests', 'netProfitLoss'));
    }
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());

        $moneyInData = MoneyIn::with('project', 'user')
            ->whereBetween('payment_datetime', [$startDate, $endDate])
            ->orderBy('payment_datetime', 'desc')
            ->get();

        return view('money_in.money_in_list', compact('moneyInData', 'startDate', 'endDate'));
    }
    public function moneyOutIndex(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());

        $moneyOutData = MoneyOut::with('project', 'user')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->orderBy('payment_date', 'desc')
            ->get();

        return view('money_out.money_out_list', compact('moneyOutData', 'startDate', 'endDate'));
    }

    public function pendingRequests(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $requests = MoneyRequest::where('status', 'pending')
                ->where('amanager_id', NULL)
                ->where('admin_status', 'approved')
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->get();
        return view('supervisor.money_requests', [
            'requests' => $requests,
            'title' => 'Pending Money Requests',
            'status' => 'pending',
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function approvedRequests(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $requests = MoneyRequest::where('status', 'approved')
                ->where('amanager_id','!=', NULL)
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->get();
        return view('supervisor.money_requests', [
            'requests' => $requests,
            'title' => 'Approved Money Requests',
            'status' => 'approved',
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function rejectedRequests(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $requests = MoneyRequest::where('status', 'rejected')
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->get();
        return view('supervisor.money_requests', [
            'requests' => $requests,
            'title' => 'Rejected Money Requests',
            'status' => 'rejected',
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    public function userProjects($id)
    {
        $user = User::findOrFail($id);
        // $projects = $user->projects()->with(['supervisors', 'moneyRequests'])->get();
        $projects = Project::all();

        $overallBalance = 0;

        foreach ($projects as $project) {
            $balanceDetails = $user->calculateProjectBalanceNewOwner($project->id);
            
            $project->approvedRequests = $balanceDetails['approvedRequests'];
            $project->approvedChallans = $balanceDetails['approvedChallans'];
            $project->supervisorBalance = $balanceDetails['projectBalance'];

            $overallBalance += $balanceDetails['projectBalance'];
        }

        $user->balance = $user->calculateBalanceUserwise($user->id);
        
        return view('account_manager.user_projects', compact('user', 'projects', 'overallBalance'));
    }


    public function getUserList(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        // dd(Auth::user()->roles->pluck('name'));
        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['manager', 'supervisor']);
        })->get();

        foreach ($users as $user) {
            $user->balance = $user->calculateBalanceUserwise($user->id);
        }

        // return view('owner.users', compact('users'));
        return view('account_manager.users', [
            'users' => $users,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

}
