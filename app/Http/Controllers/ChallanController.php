<?php

namespace App\Http\Controllers;

use App\Models\Challan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChallanController extends Controller
{
    public function index(Project $project, Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $challans = $project->challans()->with('user')
                ->whereDate('bill_date', '>=', $startDate)
                ->whereDate('bill_date', '<=', $endDate)
                ->get();
        // return view('challans.index', compact('project', 'challans'));

        
        return view('challans.index', [
            'project' => $project,
            'challans' => $challans,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);  
    }
    public function create1(Project $project)
    {
        return view('challans.create', compact('project'));
    }
    public function create(Project $project = null)
    {
        $projects = Project::where('status', '!=', 'finished')->get();
        return view('challans.create', compact('projects', 'project'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bill_date' => 'required|date',
            'amount' => 'required|numeric',
            'payment_type' => 'required|string',
            'expense_category' => 'required|string',
            'upload_image.*' => 'required|file|mimes:pdf',
            'note' => 'nullable|string',
        ]);

        $files = [];
        $folderPath = 'challans';
        // if ($request->hasFile('upload_image')) {
        //     foreach ($request->file('upload_image') as $file) {
        //         $path = $file->store('challans', 'public');
        //         $files[] = $path;
        //     }
        // }
        if (!Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->makeDirectory($folderPath);
        }
    
        if ($request->hasFile('upload_image')) {
            foreach ($request->file('upload_image') as $file) {
                // Original filename
                $originalName = $file->getClientOriginalName();
                $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $filePath = 'challans/' . $originalName;
                $counter = 1;
    
                // Check if file exists and append counter if necessary
                while (Storage::disk('public')->exists($filePath)) {
                    $filePath = 'challans/' . $fileName . $counter . '.' . $extension;
                    $counter++;
                }
    
                // Store the file with the resolved filename
                $path = $file->storeAs('challans', basename($filePath), 'public');
                $files[] = $path;
            }
        }
        
        $challan = Challan::create([
            'project_id' => $request->input('project_id'),
            'user_id' => Auth::id(),
            'bill_date' => $request->bill_date,
            'amount' => $request->amount,
            'payment_type' => $request->payment_type,
            'expense_category' => $request->expense_category,
            'upload_image' => $files ? json_encode($files) : null,
            'note' => $request->note,
            'status' => 'pending',
        ]);
        return redirect()->route('projects.challans.index', $request->input('project_id'))->with('success', 'Challan created successfully.');
    }

    public function show(Project $project, Challan $challan)
    {
        $projects = Project::all();
        // dd($projects);
        return view('challans.show', compact('projects', 'challan'));
    } 
    public function destroy($id)
    {
        $challan = Challan::findOrFail($id);
        $challan->delete();

        return redirect()->route('account-manager.dashboard')->with('success', 'Project deleted successfully.');
    }
    public function updateStatus(Request $request, Challan $challan)
    {
        if (Auth::user()->hasRole('account manager')) {
            $challan->project_id = $request->input('project_id');
            $challan->expense_category = $request->input('expense_category');
        }
        $challan->status = $request->input('status');
        $challan->note = $request->input('note');
        $challan->save();

        return redirect()->route('account-manager.dashboard')->with('success', 'Challan status updated successfully.');
    }
    public function approved(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $challans = Challan::with('project')
                            ->where('status', 'approved')
                            ->whereDate('bill_date', '>=', $startDate)
                            ->whereDate('bill_date', '<=', $endDate)
                            ->when(!$user->hasRole('account manager'), function ($query) use ($userId) {
                                $query->where('user_id', $userId);
                            })
                            ->orderBy('bill_date', 'desc')
                            ->get();
        return view('challans.list', ['challans' => $challans, 'title' => 'List of Approved Bill/Challans','startDate' => $startDate, 'endDate' => $endDate]);
    }

    public function pending(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $challans = Challan::with('project')
                            ->where('status', 'pending')
                            ->whereDate('bill_date', '>=', $startDate)
                            ->whereDate('bill_date', '<=', $endDate)
                            ->when(!$user->hasRole('account manager'), function ($query) use ($userId) {
                                $query->where('user_id', $userId);
                            })
                            ->orderBy('bill_date', 'desc')
                            ->get();
        return view('challans.list', ['challans' => $challans, 'title' => 'List of Pending Bill/Challans', 'startDate' => $startDate, 'endDate' => $endDate]);
    }

    public function rejected(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateTimeString());
        $challans = Challan::with('project')
                            ->where('status', 'rejected')
                            ->whereDate('bill_date', '>=', $startDate)
                            ->whereDate('bill_date', '<=', $endDate)
                            ->when(!$user->hasRole('account manager'), function ($query) use ($userId) {
                                $query->where('user_id', $userId);
                            })
                            ->orderBy('bill_date', 'desc')
                            ->get();
        return view('challans.list', ['challans' => $challans, 'title' => 'List of Rejected Bill/Challans', 'startDate' => $startDate, 'endDate' => $endDate]);
    }

}
