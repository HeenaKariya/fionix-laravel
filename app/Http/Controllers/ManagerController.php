<?php

namespace App\Http\Controllers;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\MoneyRequest;
use App\Models\Challan;
use App\Models\MoneyIn;
use App\Models\MoneyOut;
use Illuminate\Support\Facades\Auth;


class ManagerController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $projects = Project::with('supervisors')->where('status', '!=', 'finished')->get();
        
        $user = Auth::user();
        
        // $projects = $user->projects()->where('status', '!=', 'finished')->get();
        $overallBalance = $user->calculateBalance();
        // dd($projects, $overallBalance);
        foreach ($projects as $project) {
            $project->supervisorBalance = $user->calculateProjectBalance($project->id);
        }
        // return view('manager.dashboard', compact('projects', 'overallBalance'));
        return view('manager.dashboard', [
            'projects' => $projects,
            'overallBalance' => $overallBalance,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    public function finishedProjects(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $projects = Project::with('supervisors')->where('status', 'finished')->get();
        // return view('manager.finished_projects', compact('projects'));
        return view('manager.finished_projects', [
            'projects' => $projects,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }


    public function allRequests(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $moneyRequests = MoneyRequest::with('project')
                ->orderBy('updated_at', 'desc')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->get();
        // return view('manager.all', compact('moneyRequests'));
        return view('manager.all', [
            'moneyRequests' => $moneyRequests,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }


    public function home(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        // $projects = $user->projects()->with('supervisors')->get();
        $projects = Project::all();

        $overallBalance = $user->calculateBalance();
        foreach ($projects as $project) {
            $project->supervisorBalance = $user->calculateProjectBalance($project->id);
        }

        return view('manager.home', [
            'user' => $user,
            'projects' => $projects,
            'overallBalance' => $overallBalance,
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
        
        return view('manager.user_projects', compact('user', 'projects', 'overallBalance'));
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
        return view('manager.users', [
            'users' => $users,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
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
}
