<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ClassService;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ClassController extends Controller
{
    protected ClassService $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    public function index(): View
    {
        $classes = $this->classService->getAllClassesForAdmin();
        return view('admin.classes.index', compact('classes'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $this->classService->createClass($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Class created successfully.'
            ]);
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function update(Request $request, Classes $class)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $updatedClass = $this->classService->updateClass($class->id, $validated);

        if (!$updatedClass) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Class not found.',
                ], 404);
            }

            return back()->withErrors(['error' => 'Class not found.']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Class updated successfully.'
            ]);
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(Classes $class): RedirectResponse
    {
        $success = $this->classService->deleteClass($class->id);

        if (!$success) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Class not found or could not be deleted.');
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}