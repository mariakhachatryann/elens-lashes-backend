<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContactService;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    protected ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index(): View
    {
        $contacts = $this->contactService->getAllContactsForAdmin();
        return view('admin.contacts.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        if ($request->has('social_links') && is_string($request->social_links)) {
            $socialLinks = json_decode($request->social_links, true);
            $request->merge(['social_links' => $socialLinks]);
        }

        $validated = $request->validate([
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
            'social_links.*' => 'nullable|url',
        ]);

        $this->contactService->createContact($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully.'
            ]);
        }

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact created successfully.');
    }

    public function update(Request $request, Contact $contact)
    {
        if ($request->has('social_links') && is_string($request->social_links)) {
            $socialLinks = json_decode($request->social_links, true);
            $request->merge(['social_links' => $socialLinks]);
        }

        $validated = $request->validate([
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
            'social_links.*' => 'nullable|url',
        ]);

        $updatedContact = $this->contactService->updateContact($contact->id, $validated);

        if (!$updatedContact) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Contact not found.',
                ], 404);
            }

            return back()->withErrors(['error' => 'Contact not found.']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully.'
            ]);
        }

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $success = $this->contactService->deleteContact($contact->id);

        if (!$success) {
            return redirect()->route('admin.contacts.index')
                ->with('error', 'Contact not found or could not be deleted.');
        }

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }
}