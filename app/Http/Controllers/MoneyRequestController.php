<?php

namespace App\Http\Controllers;

use App\Models\MoneyRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Challan;

class MoneyRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'payment_type' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $data = [
            'project_id' => $request->project_id,
            'user_id' => Auth::id(),
            'date' => $request->date,
            'amount' => $request->amount,
            'payment_type' => $request->payment_type,
            'status' => 'pending',
        ];

        // Check if the logged-in user has the role 'manager'
        if (Auth::user()->hasRole('manager')) {
            $data['manager_status'] = 'approved';
            $data['manager_note'] = $request->note;
            $data['manager_id'] = Auth::id();
            $data['manager_status_updated_at'] = now();
            
            MoneyRequest::create($data);
            return redirect()->route('my.pending.requests')->with('success', 'Money request created successfully.');
        } else {
            $data['note'] = $request->note;
            $moneyRequest = MoneyRequest::create($data);
            return redirect()->route('projects.approve', ['id' => $moneyRequest->id, 'source' => 'store'])
                         ->with('success', 'Money request created successfully.');
            // return redirect()->route('projects.approve', $moneyRequest->id)->with('success', 'Money request created successfully.');
            
        }

        
    }
    
    public function store_old(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'payment_type' => 'required|string',
            'note' => 'nullable|string',
        ]);

        MoneyRequest::create([
            'project_id' => $request->project_id,
            'user_id' => Auth::id(),
            'date' => $request->date,
            'amount' => $request->amount,
            'payment_type' => $request->payment_type,
            'note' => $request->note,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Money request created successfully.');
    }
    public function create()
    {
        $projects = Project::where('status', '!=', 'finished')->get();

        return view('projects.money_requests', compact('projects'));
    }
    public function updateStatus1(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'note' => 'nullable|string',
        ]);

        $moneyRequest = MoneyRequest::findOrFail($id);
        $moneyRequest->status = $request->status;
        $moneyRequest->note = $request->note;
        $moneyRequest->save();

        return redirect()->back()->with('success', 'Money request status updated successfully.');
    }
        
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user->hasRole('owner')) {
            $request->validate([
                'status' => 'required|in:approved,rejected',
                'admin_note' => 'nullable|string',
            ]);

            $moneyRequest = MoneyRequest::findOrFail($id);
            if ($request->input('amount') != $moneyRequest->amount) {
                $moneyRequest->old_amount = $moneyRequest->amount;
                $moneyRequest->amount = $request->input('amount');
            }
            $moneyRequest->admin_status = $request->status;
            $moneyRequest->admin_note = $request->admin_note;
            if($request->status == 'rejected') {
                $moneyRequest->status = 'rejected';
            }
            // if($request->status == 'approved') {
            //     $moneyRequest->status = 'approved';
            // }
            $moneyRequest->admin_status = $request->status;
            $moneyRequest->admin_status_updated_at = now();
            $moneyRequest->admin_id = Auth::id();
            $moneyRequest->save();
        } elseif ($user->hasRole('manager')) {
            $request->validate([
                'status' => 'required|in:approved,rejected',
                'note' => 'nullable|string',
            ]);

            $moneyRequest = MoneyRequest::findOrFail($id);
            $moneyRequest->manager_status = $request->status;
            $moneyRequest->manager_note = $request->note;
            if($request->status == 'rejected') {
                $moneyRequest->status = 'rejected';
            }
            $moneyRequest->manager_status = $request->status;
            $moneyRequest->manager_status_updated_at = now();
            $moneyRequest->manager_id = Auth::id();
            $moneyRequest->save();
        } elseif($user->hasRole('account manager')) {
                $moneyRequest = MoneyRequest::findOrFail($id);
                $moneyRequest->amanager_status = $request->amanager_status;
                $moneyRequest->amanager_note = $request->amanager_note;
                if($request->amanager_status == 'approved'){
                $moneyRequest->status = 'approved';
                }
                // if($request->amanager_status == 'rejected') {
                // $moneyRequest->status = 'rejected';
                // }
                $moneyRequest->amanager_status = $request->amanager_status;
                $moneyRequest->amanager_status_updated_at = now();
                $moneyRequest->amanager_id = Auth::id();
                $moneyRequest->save();
                
                
            
        } else {
            return abort(403, 'Unauthorized action.');
        }

        return redirect()->back()->with('success', 'Money request status updated successfully.');
    }

    public function approve($id)
    {
        $moneyRequest = MoneyRequest::with(['user', 'manager', 'admin', 'amanager'])->findOrFail($id);
        $balance = null;
        $projectId = $moneyRequest->project_id;
        $userId = $moneyRequest->user_id;

        $pendingChallansTotal = Auth::user()->allPendingChallan($userId);
        $pendingChallansProjectwise = Auth::user()->pendingthisProjectChallan($projectId, $userId);
        $user = User::findOrFail($userId);
        $overallBalance = $user->calculateBalance();
        
        if (Auth::user()->hasRole('supervisor')) {
            $projectbalance = Auth::user()->moneyRequestProjectnew(Auth::id(), $projectId);
            return view('projects.approve', compact('moneyRequest','projectbalance', 'pendingChallansTotal', 'pendingChallansProjectwise','overallBalance'));
        }
        elseif (Auth::user()->hasRole('manager')) {
            $projectbalance = Auth::user()->moneyRequestProject(Auth::id());
            return view('projects.approve', compact('moneyRequest','projectbalance', 'pendingChallansTotal', 'pendingChallansProjectwise', 'overallBalance'));
        }
        else{
            $userId = $moneyRequest->user_id;
            $overallBalance1 = $user->calculateBalance($userId);
            $projectbalance = $user->moneyRequestProjectnew($userId, $projectId);
            return view('projects.approve', compact('moneyRequest','projectbalance', 'pendingChallansTotal', 'pendingChallansProjectwise', 'overallBalance','overallBalance1'));
        }
    }
    public function updateStatus11(Request $request, MoneyRequest $moneyRequest)
    {
        $moneyRequest->status = $request->input('status');
        $moneyRequest->save();

        return redirect()->back()->with('success', 'Money request status updated successfully.');
    }


    public function destroy($id)
    {
        $request = MoneyRequest::findOrFail($id);
        $request->delete();

        return redirect()->route('account-manager.moneyRequests.pending')->with('success', 'Project deleted successfully.');
    }


    public function pendingRequests(Request $request)
    {
        // $moneyRequests = MoneyRequest::with('project')->where('status', 'pending')->orderBy('updated_at', 'desc')->get();
        // return view('manager.pending', compact('moneyRequests'));
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString()) ;
        if ($user->hasRole('manager')) {
            $moneyRequests = MoneyRequest::with('project')
                ->where('status', 'pending')
                ->where('user_id', '!=', $user->id)
                ->whereNull('amanager_status')
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->orderBy('date', 'desc')
                ->get();
            // return view('manager.pending', compact('moneyRequests'));
            return view('manager.pending', [
                'moneyRequests' => $moneyRequests,
                'status' => 'pending',
                'title' => 'Pending Money Requests',
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        } elseif ($user->hasRole('owner')) {
            $moneyRequests = MoneyRequest::with(['project', 'user'])
                ->where('status', 'pending')
                ->where('manager_status', 'approved')
                ->whereNull('admin_status') 
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                // ->where('manager_status', 'approved')->orWhereNull('manager_status')

                ->orderBy('date', 'desc')
                ->get();
            // return view('owner.pending', compact('moneyRequests'));
            return view('owner.pending', [
                'requests' => $moneyRequests,
                'title' => 'Pending Money Requests',
                'status' => 'pending',
                'moneyRequests' => $moneyRequests,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        }
    }

    public function MyPendingRequests(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString()) ;
        if ($user->hasRole('manager')) {
            $moneyRequests = MoneyRequest::with('project')
            ->where('user_id', $user->id)
            ->where('manager_status', 'approved' ) 
            ->whereNull('amanager_status')
            ->where('status', 'pending' ) 
             
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date', 'desc')
            ->get();
            // dd($moneyRequests);
            // return view('manager.pending', compact('moneyRequests'));
            return view('manager.pending', [
                'moneyRequests' => $moneyRequests,
                'status' => 'pending',
                'title' => 'My Pending Money Requests',
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        }
    }

    public function rejectedRequests(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        if ($user->hasRole('manager')) {
            $moneyRequests = MoneyRequest::with('project')
                ->where('status', 'rejected')
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->orderBy('date', 'desc')
                ->get();
            // return view('manager.pending', compact('moneyRequests'));
            return view('manager.pending', [
                'moneyRequests' => $moneyRequests,
                'title' => 'Rejected Money Requests',
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        } elseif ($user->hasRole('owner')) {
            $moneyRequests = MoneyRequest::with(['project', 'user'])
                ->where('status', 'rejected')
                
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->where('manager_status', 'rejected')
                ->orderBy('date', 'desc')
                ->get();
            // return view('owner.pending', compact('moneyRequests'));
            return view('owner.pending', [
                'requests' => $moneyRequests,
                'title' => 'Rejected Money Requests',
                'status' => 'rejected',
                'moneyRequests' => $moneyRequests,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        }
    }
    
    public function MyRejectedRequests(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        if ($user->hasRole('manager')) {
            $moneyRequests = MoneyRequest::with('project')
                ->where('user_id', $user->id) 
                ->where('manager_status', 'approved')
                ->where('admin_status', 'rejected')
                ->where('status', 'rejected')
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->orderBy('date', 'desc')
                ->get();
            // return view('manager.pending', compact('moneyRequests'));
            return view('manager.pending', [
                'moneyRequests' => $moneyRequests,
                'title' => 'My Rejected Money Requests',
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        }
    }

    public function approvedRequests(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        if ($user->hasRole('manager')) {
            $moneyRequests = MoneyRequest::with('project')
                ->where('status', 'approved')
                ->where('user_id', '!=', $user->id)
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->orderBy('date', 'desc')
                ->get();
            // return view('manager.pending', compact('moneyRequests'));
            return view('manager.pending', [
                'moneyRequests' => $moneyRequests,
                'title' => 'Approved Money Requests',
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        } elseif ($user->hasRole('owner')) {
            $moneyRequests = MoneyRequest::with(['project', 'user'])
                ->where('status', 'approved')
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->where('manager_status', 'approved')
                ->orderBy('date', 'desc')
                ->get();
            // return view('owner.pending', compact('moneyRequests'));
            return view('owner.pending', [
                'requests' => $moneyRequests,
                'title' => 'Approved Money Requests',
                'status' => 'approved',
                'moneyRequests' => $moneyRequests,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        }
        
    }
    public function MyApprovedRequests(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        if ($user->hasRole('manager')) {
            $moneyRequests = MoneyRequest::with('project')
            ->where('user_id', $user->id) // Requests created by the current user
            ->where('admin_status', 'approved') // Filter by approved status
            ->where('manager_status', 'approved') // Filter by approved status
            ->where('status', 'approved') // Filter by approved status
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date', 'desc')
            ->get();
            // dd($moneyRequests);
    
            // return view('manager.pending', compact('moneyRequests'));
            return view('manager.pending', [
                'moneyRequests' => $moneyRequests,
                'title' => 'My Approved Money Requests',
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        } 
        
    }


    public function allRequests(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $moneyRequests = MoneyRequest::with('project')->orderBy('updated_at', 'desc')
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->get();
        // return view('manager.all', compact('moneyRequests'));
        return view('manager.all', [
            'moneyRequests' => $moneyRequests,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    
}
