<?php

namespace App\Http\Controllers;

use App\Models\CannedResponse;
use Illuminate\Http\Request;

class CannedResponseController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $responses = CannedResponse::latest()->get();
        return view('canned_responses', compact('responses'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'shortcut' => 'required|string|max:255|unique:canned_responses,shortcut',
            'content' => 'required|string',
        ]);

        CannedResponse::create($validated);

        return redirect()->back()->with('success', 'Canned response created successfully.');
    }

    public function update(Request $request, CannedResponse $cannedResponse)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'shortcut' => 'required|string|max:255|unique:canned_responses,shortcut,' . $cannedResponse->id,
            'content' => 'required|string',
        ]);

        $cannedResponse->update($validated);

        return redirect()->back()->with('success', 'Canned response updated successfully.');
    }

    public function destroy(CannedResponse $cannedResponse)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $cannedResponse->delete();

        return redirect()->back()->with('success', 'Canned response deleted successfully.');
    }
}
