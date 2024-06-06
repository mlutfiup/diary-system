<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Diary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DiaryController extends Controller
{
    // Fetch all diaries of the authenticated user
    public function index()
    {
        return response()->json(Diary::where('user_id', Auth::id())->get());
    }

    // Store a new diary entry
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'details' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Prepare the data for saving
        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Handle the image upload if provided
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        // Create a new diary entry
        $diary = Diary::create($data);

        // Return the created diary entry as a JSON response
        return response()->json($diary, 201);
    }

    // Show a single diary entry
    public function show($id)
    {
        // Fetch the diary entry by ID
        $diary = Diary::findOrFail($id);

        // Ensure the authenticated user owns the diary entry
        if ($diary->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Return the diary entry as a JSON response
        return response()->json($diary);
    }

    // Update an existing diary entry
    public function update(Request $request, $id)
    {
        // Fetch the diary entry by ID
        $diary = Diary::findOrFail($id);

        // Ensure the authenticated user owns the diary entry
        if ($diary->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate the incoming request data
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'details' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Prepare the data for updating
        $data = $request->all();

        // Handle the image upload if provided
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($diary->image) {
                Storage::delete('public/' . $diary->image);
            }
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        // Update the diary entry
        $diary->update($data);

        // Return the updated diary entry as a JSON response
        return response()->json($diary);
    }

    // Delete a diary entry
    public function destroy($id)
    {
        // Fetch the diary entry by ID
        $diary = Diary::findOrFail($id);

        // Ensure the authenticated user owns the diary entry
        if ($diary->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete the image if it exists
        if ($diary->image) {
            Storage::delete('public/' . $diary->image);
        }

        // Delete the diary entry
        $diary->delete();

        // Return a success message
        return response()->json(['message' => 'Deleted successfully']);
    }
}
