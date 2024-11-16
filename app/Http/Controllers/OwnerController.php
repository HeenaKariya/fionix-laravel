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
use Spatie\Permission\Models\Role;
use PDF;

class OwnerController extends Controller
{
    public function pendingProjects(Request $request)
    {
        return redirect()->route('owner.pending');
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $projects = Project::with('supervisors')->where('status', 'pending')->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)->get();
        // total expenses and revenues for each project
        $totalExpenses = [];
        $totalRevenues = [];

        foreach ($projects as $project) {
            $projectId = $project->id;

            // Fetch Total Expense for each project
            $totalExpenseSum = Challan::where('project_id', $projectId)
                ->where('status', 'approved')
                ->sum('amount') ?? 0;
            
            // Fetch Total Revenue for each project
            $totalRevenue = MoneyIn::where('project_id', $projectId)
                ->sum('amount') ?? 0;
                // dd($totalRevenue);
            // Store the calculated values in arrays using projectId as key
            $totalExpenses[$projectId] = $totalExpenseSum;
            $totalRevenues[$projectId] = $totalRevenue;
        }
        // return view('owner.dashboard', compact('projects', 'totalExpenses', 'totalRevenues'));
        return view('owner.dashboard', [
            'projects' => $projects,
            'totalExpenses' => $totalExpenses,
            'totalRevenues' => $totalRevenues,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

    }
    
    public function userProjects1($id)
    {
        $user = User::findOrFail($id);
        $projects = $user->projects()->with(['supervisors', 'moneyRequests'])->get();

        $overallBalance = 0;

        foreach ($projects as $project) {
            $project->supervisorBalance = $user->calculateProjectBalanceNew($project->id);
            $overallBalance += $project->supervisorBalance;
        }
        $user->balance = $user->calculateBalanceUserwise($user->id);
        
        return view('owner.user_projects', compact('user', 'projects', 'overallBalance'));
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
        
        return view('owner.user_projects', compact('user', 'projects', 'overallBalance'));
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
        return view('owner.users', [
            'users' => $users,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    public function activeProjects(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $projects = Project::with('supervisors')->where('status', '!=', 'finished')->get();
        
        // total expenses and revenues for each project
        $totalExpenses = [];
        $totalRevenues = [];

        foreach ($projects as $project) {
            $projectId = $project->id;

            // Fetch Total Expense for each project
            $totalExpenseSum = Challan::where('project_id', $projectId)
                ->where('status', 'approved')
                ->sum('amount') ?? 0;
            
            // Fetch Total Revenue for each project
            $totalRevenue = MoneyIn::where('project_id', $projectId)
                ->sum('amount') ?? 0;
                // dd($totalRevenue);
            // Store the calculated values in arrays using projectId as key
            $totalExpenses[$projectId] = $totalExpenseSum;
            $totalRevenues[$projectId] = $totalRevenue;
        }

        // Pass the arrays to the view
        // return view('owner.active_projects', compact('projects', 'totalExpenses', 'totalRevenues'));
        return view('owner.dashboard', [
            'projects' => $projects,
            'totalExpenses' => $totalExpenses,
            'totalRevenues' => $totalRevenues,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }


    public function finishedProjects(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $projects = Project::with('supervisors')->where('status', 'finished')->get();
        // total expenses and revenues for each project
        $totalExpenses = [];
        $totalRevenues = [];

        foreach ($projects as $project) {
            $projectId = $project->id;

            // Fetch Total Expense for each project
            $totalExpenseSum = Challan::where('project_id', $projectId)
                ->where('status', 'approved')
                ->sum('amount') ?? 0;
            
            // Fetch Total Revenue for each project
            $totalRevenue = MoneyIn::where('project_id', $projectId)
                ->sum('amount') ?? 0;
                // dd($totalRevenue);
            // Store the calculated values in arrays using projectId as key
            $totalExpenses[$projectId] = $totalExpenseSum;
            $totalRevenues[$projectId] = $totalRevenue;
        }
        // return view('owner.finished_projects', compact('projects', 'totalExpenses', 'totalRevenues'));
        return view('owner.finished_projects', [
            'projects' => $projects,
            'totalExpenses' => $totalExpenses,
            'totalRevenues' => $totalRevenues,
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
