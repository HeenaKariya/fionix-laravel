<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\MoneyRequest;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $projects = $user->projects()->where('status', '!=', 'finished')->get();
        $overallBalance = $user->calculateBalance();
        // dd($projects, $overallBalance);
        foreach ($projects as $project) {
            $project->supervisorBalance = $user->calculateProjectBalance($project->id);
        }
        return view('supervisor.active_projects', [
            'projects' => $projects,
            'overallBalance' => $overallBalance,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
        // return view('supervisor.active_projects', compact('projects', 'overallBalance'));
        // return view('supervisor.dashboard', compact('projects'));
    }
    public function finishedProjects()
    {
        $user = Auth::user();
        $projects = $user->projects()->where('status', 'finished')->get(); 
        $overallBalance = $user->calculateBalance();
        // dd($projects, $overallBalance);
        foreach ($projects as $project) {
            $project->supervisorBalance = $user->calculateProjectBalance($project->id);
        }
        
        return view('supervisor.finished_projects', compact('projects', 'overallBalance'));
        // return view('supervisor.finished_projects', compact('projects'));
    }
    public function show(Project $project)
    {
        $user = Auth::user();
        
        if (!$project->supervisors->contains($user)) {
            abort(403, 'Unauthorized action.');
        }

        return view('projects.show', compact('project'));
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

        // return view('supervisor.home', compact('user', 'projects', 'overallBalance'));
        return view('supervisor.home', [
            'user' => $user,
            'projects' => $projects,
            'overallBalance' => $overallBalance,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }    

    public function pendingRequests(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $requests = MoneyRequest::where('user_id', $userId)
                ->where('status', 'pending')
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
        $requests = MoneyRequest::where('user_id', $userId)
                ->where('status', 'approved')
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
        $requests = MoneyRequest::where('user_id', $userId)
                ->where('status', 'rejected')
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




}
