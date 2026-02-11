@extends('layouts.admin')

@section('title', 'Team Members')

@section('content')
<div class="space-y-6" x-data="teamManager()">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Team Members</h1>
        <button @click="openCreateModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
            Add Member
        </button>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($members as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($member->image_url)
                            <img src="{{ $member->image_url }}"
                                 alt="{{ $member->name }}"
                                 class="h-12 w-12 rounded-full object-cover">
                        @else
                            <span class="text-gray-400">No photo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $member->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $member->role ?? '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $member->sort_order }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($member->is_active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                        <button
                            @click="openEditModal({{ $member->id }}, @js($member->name), @js($member->role), {{ $member->sort_order }}, {{ $member->is_active ? 'true' : 'false' }})"
                            class="text-blue-600 hover:text-blue-900">
                            Edit
                        </button>
                        <form method="POST" action="{{ route('admin.team-members.destroy', $member) }}" class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this member?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No team members found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
        <div class="mt-6">
            {{ $members->links() }}
        </div>
    @endif

    <!-- Edit Modal -->
    <div x-show="showEditModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeEditModal()"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <form @submit.prevent="updateMember()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Edit Team Member
                                </h3>

                                <div class="mb-4">
                                    <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Name
                                    </label>
                                    <input type="text"
                                           x-model="editForm.name"
                                           id="edit-name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label for="edit-role" class="block text-sm font-medium text-gray-700 mb-1">
                                        Role
                                    </label>
                                    <input type="text"
                                           x-model="editForm.role"
                                           id="edit-role"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="edit-sort-order" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sort Order
                                    </label>
                                    <input type="number"
                                           x-model.number="editForm.sort_order"
                                           id="edit-sort-order"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4 flex items-center">
                                    <input type="checkbox"
                                           id="edit-is-active"
                                           x-model="editForm.is_active"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="edit-is-active" class="ml-2 block text-sm text-gray-700">
                                        Active
                                    </label>
                                </div>

                                <div class="mb-4">
                                    <label for="edit-image" class="block text-sm font-medium text-gray-700 mb-1">
                                        Photo (Optional)
                                    </label>
                                    <input type="file"
                                           id="edit-image"
                                           accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <p class="mt-1 text-sm text-gray-500">Upload a new image file (JPEG, PNG, JPG, GIF, WEBP - max 2MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                :disabled="loading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!loading">Update Member</span>
                            <span x-show="loading">Updating...</span>
                        </button>
                        <button type="button"
                                @click="closeEditModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="showCreateModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeCreateModal()"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <form @submit.prevent="createMember()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Add Team Member
                                </h3>

                                <div class="mb-4">
                                    <label for="create-name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Name
                                    </label>
                                    <input type="text"
                                           x-model="createForm.name"
                                           id="create-name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label for="create-role" class="block text-sm font-medium text-gray-700 mb-1">
                                        Role
                                    </label>
                                    <input type="text"
                                           x-model="createForm.role"
                                           id="create-role"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="create-sort-order" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sort Order
                                    </label>
                                    <input type="number"
                                           x-model.number="createForm.sort_order"
                                           id="create-sort-order"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4 flex items-center">
                                    <input type="checkbox"
                                           id="create-is-active"
                                           x-model="createForm.is_active"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="create-is-active" class="ml-2 block text-sm text-gray-700">
                                        Active
                                    </label>
                                </div>

                                <div class="mb-4">
                                    <label for="create-image" class="block text-sm font-medium text-gray-700 mb-1">
                                        Photo
                                    </label>
                                    <input type="file"
                                           id="create-image"
                                           accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <p class="mt-1 text-sm text-gray-500">Upload an image file (JPEG, PNG, JPG, GIF, WEBP - max 2MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                :disabled="loading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!loading">Create Member</span>
                            <span x-show="loading">Creating...</span>
                        </button>
                        <button type="button"
                                @click="closeCreateModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function teamManager() {
    return {
        showEditModal: false,
        showCreateModal: false,
        loading: false,
        editForm: {
            id: null,
            name: '',
            role: '',
            sort_order: 0,
            is_active: true,
        },
        createForm: {
            name: '',
            role: '',
            sort_order: 0,
            is_active: true,
        },

        openEditModal(id, name, role, sortOrder, isActive) {
            this.editForm = {
                id: id,
                name: name || '',
                role: role || '',
                sort_order: sortOrder ?? 0,
                is_active: !!isActive,
            };
            this.showEditModal = true;
        },

        closeEditModal() {
            this.showEditModal = false;
            this.editForm = {
                id: null,
                name: '',
                role: '',
                sort_order: 0,
                is_active: true,
            };
        },

        openCreateModal() {
            this.createForm = {
                name: '',
                role: '',
                sort_order: 0,
                is_active: true,
            };
            this.showCreateModal = true;
        },

        closeCreateModal() {
            this.showCreateModal = false;
            this.createForm = {
                name: '',
                role: '',
                sort_order: 0,
                is_active: true,
            };
        },

        async updateMember() {
            this.loading = true;

            try {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'PUT');
                formData.append('name', this.editForm.name);
                formData.append('role', this.editForm.role || '');
                formData.append('sort_order', this.editForm.sort_order ?? 0);
                formData.append('is_active', this.editForm.is_active ? '1' : '0');

                const imageFile = document.getElementById('edit-image').files[0];
                if (imageFile) {
                    formData.append('image', imageFile);
                }

                const response = await fetch(`/admin/team-members/${this.editForm.id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Error updating team member. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating team member. Please try again.');
            } finally {
                this.loading = false;
            }
        },

        async createMember() {
            this.loading = true;

            try {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('name', this.createForm.name);
                formData.append('role', this.createForm.role || '');
                formData.append('sort_order', this.createForm.sort_order ?? 0);
                formData.append('is_active', this.createForm.is_active ? '1' : '0');

                const imageFile = document.getElementById('create-image').files[0];
                if (imageFile) {
                    formData.append('image', imageFile);
                }

                const response = await fetch('/admin/team-members', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (response.ok) {
                    this.closeCreateModal();
                    window.location.reload();
                } else {
                    alert('Error creating team member. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error creating team member. Please try again.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection

