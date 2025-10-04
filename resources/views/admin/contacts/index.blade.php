@extends('layouts.admin')

@section('title', 'Contact Information')

@section('content')
<div class="space-y-6" x-data="contactManager()">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Contact Information</h1>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Social Links</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($contacts as $contact)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($contact->logo)
                            <img src="{{ asset('storage/' . $contact->logo) }}"
                                 alt="Logo"
                                 class="h-10 w-10 object-cover rounded">
                        @else
                            <span class="text-gray-400">No logo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ Str::limit($contact->address, 70) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $contact->phone }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($contact->social_links)
                            <div class="flex space-x-2">
                                @foreach($contact->social_links as $platform => $url)
                                    <a href="{{ $url }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-800 text-xs">
                                        {{ ucfirst($platform) }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400">No social links</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button @click="openEditModal({{ $contact->id }}, @js($contact->address), @js($contact->phone), @js($contact->social_links), @js($contact->logo))"
                                class="text-blue-600 hover:text-blue-900">Edit</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        No contacts found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contacts->hasPages())
        <div class="mt-6">
            {{ $contacts->links() }}
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

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <form @submit.prevent="updateContact()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Edit Contact
                                </h3>

                                <div class="mb-4">
                                    <label for="edit-address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Address
                                    </label>
                                    <textarea x-model="editForm.address"
                                              id="edit-address"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="edit-phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Phone
                                    </label>
                                    <input type="tel"
                                           x-model="editForm.phone"
                                           id="edit-phone"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="edit-logo" class="block text-sm font-medium text-gray-700 mb-1">
                                        Logo
                                    </label>
                                    <div class="flex items-center space-x-4">
                                        <div x-show="editForm.logo || editForm.logoPreview" class="flex-shrink-0">
                                            <img :src="editForm.logoPreview || (editForm.logo ? '/storage/' + editForm.logo : '')"
                                                 alt="Current logo"
                                                 class="h-16 w-16 object-cover rounded border">
                                        </div>
                                        <div class="flex-1">
                                            <input type="file"
                                                   @change="handleLogoUpload($event)"
                                                   id="edit-logo"
                                                   accept="image/*"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <p class="text-xs text-gray-500 mt-1">Upload a new logo (JPEG, PNG, JPG, GIF, SVG - Max 2MB)</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Social Media Links
                                    </label>
                                    <div class="space-y-2">
                                        <template x-for="(link, index) in editForm.social_links" :key="index">
                                            <div class="flex items-center space-x-2">
                                                <input type="text"
                                                       x-model="link.platform"
                                                       placeholder="Platform (e.g., instagram)"
                                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                <input type="url"
                                                       x-model="link.url"
                                                       placeholder="URL"
                                                       class="flex-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                <button type="button"
                                                        @click="removeSocialLink(index)"
                                                        class="px-3 py-2 text-red-600 hover:text-red-800">
                                                    Remove
                                                </button>
                                            </div>
                                        </template>
                                        <button type="button"
                                                @click="addSocialLink()"
                                                class="px-4 py-2 text-sm text-blue-600 hover:text-blue-800 border border-blue-300 rounded-md">
                                            Add Social Link
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                :disabled="loading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!loading">Update Contact</span>
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

</div>

<script>
function contactManager() {
    return {
        showEditModal: false,
        loading: false,
        editForm: {
            id: null,
            address: '',
            phone: '',
            social_links: [],
            logo: '',
            logoPreview: ''
        },

        openEditModal(id, address, phone, socialLinks, logo) {
            this.editForm = {
                id: id,
                address: address || '',
                phone: phone || '',
                social_links: socialLinks ? Object.entries(socialLinks).map(([platform, url]) => ({ platform, url })) : [],
                logo: logo || '',
                logoPreview: ''
            };
            this.showEditModal = true;
        },

        closeEditModal() {
            this.showEditModal = false;
            this.editForm = {
                id: null,
                address: '',
                phone: '',
                social_links: [],
                logo: '',
                logoPreview: ''
            };
        },

        addSocialLink() {
            this.editForm.social_links.push({ platform: '', url: '' });
        },

        removeSocialLink(index) {
            this.editForm.social_links.splice(index, 1);
        },

        handleLogoUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Create a preview URL for the selected file
                const reader = new FileReader();
                reader.onload = (e) => {
                    // Store the preview URL for display
                    this.editForm.logoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },


        async updateContact() {
            this.loading = true;

            try {
                const socialLinksObj = {};
                this.editForm.social_links.forEach(link => {
                    if (link.platform && link.url) {
                        socialLinksObj[link.platform] = link.url;
                    }
                });

                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'PUT');
                formData.append('address', this.editForm.address);
                formData.append('phone', this.editForm.phone);
                formData.append('social_links', JSON.stringify(socialLinksObj));

                const logoFile = document.getElementById('edit-logo').files[0];
                if (logoFile) {
                    formData.append('logo', logoFile);
                }

                const response = await fetch(`/admin/contacts/${this.editForm.id}`, {
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
                    alert('Error updating contact. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating contact. Please try again.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
