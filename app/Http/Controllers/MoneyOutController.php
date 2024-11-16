<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MoneyOut;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MoneyOutController extends Controller
{
    public function create()
    {
        $projects = Project::all();
        return view('money_out.create', compact('projects'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'transaction_id' => 'required|string',
            'from' => 'required|string',
            'to' => 'required|string',
            'payment_type' => 'required|string',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);

        $moneyOut = new MoneyOut($request->except('upload_image'));
        $moneyOut->user_id = auth()->id();

        
        
        if ($request->hasFile('upload_image')) {
            $files = [];
            foreach ($request->file('upload_image') as $file) {
                if (!$file->isValid()) {
                    return redirect()->back()->with('error', 'Invalid file uploaded.');
                }
        
                $originalName = $file->getClientOriginalName();
                $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $filePath = 'challans/' . $originalName;
                $counter = 1;
        
                while (Storage::disk('public')->exists($filePath)) {
                    $filePath = 'challans/' . $fileName . $counter . '.' . $extension;
                    $counter++;
                }
        
                $path = $file->storeAs('challans', basename($filePath), 'public');
                $files[] = $path;
            }
            $moneyOut->image = json_encode($files);
        }

        $moneyOut->save();
        // dd('hi');
        return redirect()->route('money_out.index')->with('success', 'Money Out record created successfully!');
    }

}
