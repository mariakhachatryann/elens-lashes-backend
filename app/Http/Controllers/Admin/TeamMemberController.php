<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Services\TeamMemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamMemberController extends Controller
{
    protected TeamMemberService $teamMemberService;

    public function __construct(TeamMemberService $teamMemberService)
    {
        $this->teamMemberService = $teamMemberService;
    }

    public function index(): View
    {
        $members = $this->teamMemberService->getAllForAdmin();

        return view('admin.team-members.index', compact('members'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $this->teamMemberService->create($validated, $request->file('image'));

        return redirect()
            ->route('admin.team-members.index')
            ->with('success', 'Team member created successfully.');
    }

    public function update(Request $request, TeamMember $team_member): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $updated = $this->teamMemberService->update($team_member->id, $validated, $request->file('image'));

        if (!$updated) {
            return back()->withErrors(['error' => 'Team member not found.']);
        }

        return redirect()
            ->route('admin.team-members.index')
            ->with('success', 'Team member updated successfully.');
    }

    public function destroy(TeamMember $team_member): RedirectResponse
    {
        $success = $this->teamMemberService->delete($team_member->id);

        if (!$success) {
            return redirect()
                ->route('admin.team-members.index')
                ->with('error', 'Team member not found or could not be deleted.');
        }

        return redirect()
            ->route('admin.team-members.index')
            ->with('success', 'Team member deleted successfully.');
    }
}

