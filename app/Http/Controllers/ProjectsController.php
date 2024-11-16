<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Models\MoneyRequest;
use App\Models\MoneyIn;
use App\Models\MoneyOut;
use App\Models\Challan;
use Spatie\Permission\Models\Role;
use PDF;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return view('manager.dashboard', compact('projects'));
    }

    public function create()
    {
        $supervisors = \Spatie\Permission\Models\Role::where('name', 'supervisor')->first()->users;
        return view('projects.create', compact('supervisors'));
    }

    public function generatePdf(Project $project)
    {
        $projectId = $project->id;

        $moneyInTransactions = MoneyIn::where('project_id', $projectId)
            ->with('user')
            ->orderBy('payment_datetime', 'desc')
            ->get();

        $moneyOutTransactions = MoneyOut::where('project_id', $projectId)
            ->with('user')
            ->orderBy('payment_date', 'desc')
            ->get();

        $approvedBillChallan = Challan::where('project_id', $projectId)
            ->with('user')
            ->where('status', 'approved')
            ->get();

        // $combinedOutTransactions = collect($moneyOutTransactions)->merge($approvedBillChallan)->sortBy(function ($item) {
        //     return $item->payment_date ?? $item->date ?? now();
        // });

        $moneyOutTransactions = collect($moneyOutTransactions)->map(function ($item) {
            $item->type = 'money_out';
            return $item;
        });
        
        $approvedBillChallan = collect($approvedBillChallan)->map(function ($item) {
            $item->type = 'challan';
            return $item;
        });
        
        $combinedOutTransactions = collect($moneyOutTransactions)
            ->merge($approvedBillChallan)
            ->sortBy(function ($item) {
                if ($item->type === 'money_out') {
                    return $item->payment_date ?? now();
                } elseif ($item->type === 'challan') {
                    return $item->bill_date ?? now();
                }
                return now();
        });

        $totalReceived = $moneyInTransactions->sum('amount');
        $totalPayment = $combinedOutTransactions->sum('amount');
        $netResult =  $totalReceived - $totalPayment;
        $netStatus = $netResult > 0 ? 'Net Profit' : 'Net Loss';

        $userBalances = [];
        $users = User::all();  
        $totalUserBalance = 0;
        foreach ($users as $user) {
            $approvedRequestsProjectwise = MoneyRequest::where('user_id', $user->id)
                ->where('project_id', $projectId)
                ->where('amanager_status', 'approved')
                ->sum('amount') ?? 0;

            $approvedChallansProjectwise = Challan::where('user_id', $user->id)
                ->where('project_id', $projectId)
                ->where('status', 'approved')
                ->sum('amount') ?? 0;

            $userBalance = $approvedRequestsProjectwise - $approvedChallansProjectwise;
            $userBalances[$user->name] = $userBalance;
            if ($userBalance != 0) {
                $totalUserBalance += $userBalance;
            }
        }
        $totalPayment = $combinedOutTransactions->sum('amount') + $totalUserBalance;
        $netResult = $totalReceived - ($totalPayment) ;
        $netStatus = $netResult > 0 ? 'Net Profit' : 'Net Loss';
        $pdf = PDF::loadView('projects.pdf', [
            'project' => $project,
            'moneyInTransactions' => $moneyInTransactions,
            'combinedOutTransactions' => $combinedOutTransactions,
            'totalReceived' => $totalReceived,
            'totalPayment' => $totalPayment,
            'netResult' => $netResult,
            'netStatus' => $netStatus,
            'userBalances' => $userBalances,
        ])->setPaper('a4');

        $projectName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $project->name);
        return $pdf->download($projectName . '_income_expense_report.pdf');

    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|string',
            'supervisors' => 'required|array',
            'note' => 'nullable|string',
            'budget' => 'nullable|numeric',
        ]);

        $project = new Project();
        $project->name = $request->name;
        $project->location = $request->location;
        $project->start_date = $request->start_date;
        $project->status = $request->status;
        $project->note = $request->note;
        $project->budget = $request->budget;
        $project->save();

        // Attach supervisors
        $supervisorIds = explode(',', $request->supervisors[0]);
        $project->supervisors()->attach($supervisorIds);

        return redirect()->route('manager.dashboard')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'status' => 'required|string',
            'supervisors' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
    public function getSupervisors()
    {
        $supervisors = User::role('supervisor')->get(); 
        return response()->json($supervisors);
    }
}
