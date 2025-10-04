<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ServiceService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ServiceController extends Controller
{
    protected ServiceService $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index(): View
    {
        $services = $this->serviceService->getAllServicesForAdmin();
        return view('admin.services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'parent_id' => 'nullable|integer|exists:services,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $this->serviceService->createService($validated, $request->file('image'));

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Service created successfully.'
                ]);
            }

            return redirect()->route('admin.services.index')
                ->with('success', 'Service created successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => ['image' => [$e->getMessage()]]
                ], 422);
            }

            return back()->withErrors(['image' => $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, Service $service)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'parent_id' => 'nullable|integer|exists:services,id',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'required|file|max:2048';
        }

        $validated = $request->validate($rules);

        try {
            $updatedService = $this->serviceService->updateService($service->id, $validated, $request->file('image'));

            if (!$updatedService) {
                throw new \Exception('Service not found.');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Service updated successfully.'
                ]);
            }

            return redirect()->route('admin.services.index')
                ->with('success', 'Service updated successfully.');
        } catch (\InvalidArgumentException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => ['image' => [$e->getMessage()]]
                ], 422);
            }

            return back()->withErrors(['image' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 404);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Service $service): RedirectResponse
    {
        $success = $this->serviceService->deleteService($service->id);

        if (!$success) {
            return redirect()->route('admin.services.index')
                ->with('error', 'Service not found or could not be deleted.');
        }

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}