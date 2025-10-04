@extends('layouts.admin')

@section('title', 'Services Management')

@section('content')
<div class="space-y-6" x-data="serviceManager()">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Services</h1>
        <button @click="openCreateModal()" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Add Service
        </button>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="space-y-4 p-4">
            @forelse($services as $service)
                <li class="bg-gray-50 rounded-lg border border-gray-200">
                    <div class="px-4 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($service->image)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $service->image_url }}" alt="{{ $service->title }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <p class="text-sm font-medium text-gray-900">{{ $service->title }}</p>
                                    @if($service->parent_id)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Sub-service
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500">{{ Str::limit($service->description, 100) }}</p>
                                <div class="flex items-center mt-1">
                                    <span class="text-sm font-medium text-green-600">${{ number_format($service->price, 2) }}</span>
                                    @if($service->children->count() > 0)
                                        <span class="ml-2 text-xs text-gray-500">{{ $service->children->count() }} sub-services</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="openEditModal({{ $service->id }}, '{{ $service->title }}', '{{ addslashes($service->description) }}', {{ $service->price }}, {{ $service->parent_id ?? 'null' }})" 
                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Edit
                            </button>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this service?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    @if($service->children->count() > 0)
                        <div class="bg-gray-100 px-4 py-3 mt-3">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Sub-services</h4>
                            <div class="space-y-3">
                                @foreach($service->children as $child)
                                    <div class="flex items-center justify-between py-2 px-3 bg-white rounded border">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 mr-3">
                                                @if($child->image)
                                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ $child->image_url }}" alt="{{ $child->title }}">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $child->title }}</span>
                                            <span class="ml-2 text-xs text-green-600">${{ number_format($child->price, 2) }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button @click="openEditModal({{ $child->id }}, '{{ $child->title }}', '{{ addslashes($child->description) }}', {{ $child->price }}, {{ $child->parent_id ?? 'null' }})" 
                                                    class="text-indigo-600 hover:text-indigo-900 text-xs">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.services.destroy', $child) }}" method="POST" class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this service?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </li>
            @empty
                <li class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No services</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new service.</p>
                    <div class="mt-6">
                        <button @click="openCreateModal()" 
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Service
                        </button>
                    </div>
                </li>
            @endforelse
        </ul>
    </div>

    @if($services->hasPages())
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $services->links() }}
        </div>
    @endif

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
                
                <div>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Edit Service
                                </h3>
                                
                                <div class="mb-4">
                                    <label for="edit-title" class="block text-sm font-medium text-gray-700 mb-1">
                                        Title
                                    </label>
                                    <input type="text" 
                                           x-model="editForm.title"
                                           id="edit-title"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label for="edit-description" class="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <textarea x-model="editForm.description"
                                              id="edit-description"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="edit-price" class="block text-sm font-medium text-gray-700 mb-1">
                                        Price
                                    </label>
                                    <input type="number" 
                                           x-model="editForm.price"
                                           id="edit-price"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label for="edit-parent" class="block text-sm font-medium text-gray-700 mb-1">
                                        Parent Service (Optional)
                                    </label>
                                    <select x-model="editForm.parent_id"
                                            id="edit-parent"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">None (Main Service)</option>
                                        @foreach($services->whereNull('parent_id') as $parentService)
                                            <option value="{{ $parentService->id }}" x-show="editForm.id != {{ $parentService->id }}">{{ $parentService->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="edit-image" class="block text-sm font-medium text-gray-700 mb-1">
                                        Image (Optional)
                                    </label>
                                    <input type="file" 
                                           id="edit-image"
                                           accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="mt-1 text-sm text-gray-500">Upload a new image file (JPEG, PNG, JPG, GIF - max 2MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                @click="updateService()"
                                :disabled="loading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!loading">Update Service</span>
                            <span x-show="loading">Updating...</span>
                        </button>
                        <button type="button" 
                                @click="closeEditModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                
                <form @submit.prevent="createService()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Create New Service
                                </h3>
                                
                                <div class="mb-4">
                                    <label for="create-title" class="block text-sm font-medium text-gray-700 mb-1">
                                        Title
                                    </label>
                                    <input type="text" 
                                           x-model="createForm.title"
                                           id="create-title"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label for="create-description" class="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <textarea x-model="createForm.description"
                                              id="create-description"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="create-price" class="block text-sm font-medium text-gray-700 mb-1">
                                        Price
                                    </label>
                                    <input type="number" 
                                           x-model="createForm.price"
                                           id="create-price"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label for="create-parent" class="block text-sm font-medium text-gray-700 mb-1">
                                        Parent Service (Optional)
                                    </label>
                                    <select x-model="createForm.parent_id"
                                            id="create-parent"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">None (Main Service)</option>
                                        @foreach($services->whereNull('parent_id') as $parentService)
                                            <option value="{{ $parentService->id }}">{{ $parentService->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="create-image" class="block text-sm font-medium text-gray-700 mb-1">
                                        Image (Optional)
                                    </label>
                                    <input type="file" 
                                           id="create-image"
                                           accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="mt-1 text-sm text-gray-500">Upload an image file (JPEG, PNG, JPG, GIF - max 2MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                :disabled="loading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!loading">Create Service</span>
                            <span x-show="loading">Creating...</span>
                        </button>
                        <button type="button" 
                                @click="closeCreateModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function serviceManager() {
    return {
        showEditModal: false,
        showCreateModal: false,
        loading: false,
        editForm: {
            id: null,
            title: '',
            description: '',
            price: 0,
            parent_id: null
        },
        createForm: {
            title: '',
            description: '',
            price: 0,
            parent_id: null
        },

        openEditModal(id, title, description, price, parentId) {
            this.editForm = {
                id: id,
                title: title,
                description: description || '',
                price: price,
                parent_id: parentId
            };
            this.showEditModal = true;
        },

        closeEditModal() {
            this.showEditModal = false;
            this.editForm = {
                id: null,
                title: '',
                description: '',
                price: 0,
                parent_id: null
            };
        },

        openCreateModal() {
            this.createForm = {
                title: '',
                description: '',
                price: 0,
                parent_id: null
            };
            this.showCreateModal = true;
        },

        closeCreateModal() {
            this.showCreateModal = false;
            this.createForm = {
                title: '',
                description: '',
                price: 0,
                parent_id: null
            };
        },

        async updateService() {
            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'PUT');
                formData.append('title', this.editForm.title);
                formData.append('description', this.editForm.description);
                formData.append('price', this.editForm.price);
                formData.append('parent_id', this.editForm.parent_id || '');
                
                const imageFile = document.getElementById('edit-image').files[0];
                if (imageFile) {
                    console.log('Image file selected:', imageFile.name, imageFile.type, imageFile.size);
                    console.log('File MIME type:', imageFile.type);
                    console.log('File extension:', imageFile.name.split('.').pop());
                    formData.append('image', imageFile);
                } else {
                    console.log('No image file selected');
                }

                console.log('Sending form data:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }

                const response = await fetch(`/admin/services/${this.editForm.id}`, {
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
                    const errorData = await response.json();
                    console.error('Error response:', errorData);
                    alert('Error updating service: ' + (errorData.message || 'Please try again.'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating service. Please try again.');
            } finally {
                this.loading = false;
            }
        },

        async createService() {
            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('title', this.createForm.title);
                formData.append('description', this.createForm.description);
                formData.append('price', this.createForm.price);
                formData.append('parent_id', this.createForm.parent_id || '');
                
                const imageFile = document.getElementById('create-image').files[0];
                if (imageFile) {
                    formData.append('image', imageFile);
                }

                const response = await fetch('/admin/services', {
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
                    alert('Error creating service. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error creating service. Please try again.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
