<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WorkService;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class WorkController extends Controller
{
    protected WorkService $workService;

    public function __construct(WorkService $workService)
    {
        $this->workService = $workService;
    }

    public function index(): View
    {
        $works = $this->workService->getAllWorksForAdmin();
        return view('admin.works.index', compact('works'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $this->workService->createWork($validated, $request->file('image'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Work created successfully.'
            ]);
        }

        return redirect()->route('admin.works.index')
            ->with('success', 'Work created successfully.');
    }


    public function update(Request $request, Work $work)
    {
        if ($request->has('image') && !$request->hasFile('image')) {
            $request->request->remove('image');
        }

        $rules = [
            'title' => 'required|string|max:255',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'required|file|max:2048';
        }

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            throw $e;
        }

        $updatedWork = $this->workService->updateWork($work->id, $validated, $request->file('image'));

        if (!$updatedWork) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Work not found.',
                ], 404);
            }

            return back()->withErrors(['error' => 'Work not found.']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Work updated successfully.'
            ]);
        }

        return redirect()->route('admin.works.index')
            ->with('success', 'Work updated successfully.');
    }

    public function destroy(Work $work): RedirectResponse
    {
        $success = $this->workService->deleteWork($work->id);

        if (!$success) {
            return redirect()->route('admin.works.index')
                ->with('error', 'Work not found or could not be deleted.');
        }

        return redirect()->route('admin.works.index')
            ->with('success', 'Work deleted successfully.');
    }
}