<?php

namespace App\Http\Controllers;

use App\Models\MoneyIn;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MoneyInController extends Controller
{
    public function create()
    {
        $projects = Project::all();
        return view('money_in.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'transaction_id' => 'required|string',
            'from' => 'required|string|max:255',
            'to' => 'required|string|max:255',
            'payment_type' => 'required|string|max:255',
            'payment_datetime' => 'required|date',
            'amount' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        MoneyIn::create([
            'project_id' => $request->project_id,
            'user_id' => Auth::id(),
            'transaction_id' => $request->transaction_id,
            'from' => $request->from,
            'to' => $request->to,
            'payment_type' => $request->payment_type,
            'payment_datetime' => $request->payment_datetime,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ]);
        return redirect()->route('money_in.index')->with('success', 'Money Out record created successfully!');
        // return back()->with('success', 'Money In record created successfully.');

    }
}

