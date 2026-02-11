@extends('layouts.admin')

@section('title', 'About Us')

@section('content')
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">About Us & Header Settings</h1>

        <div class="bg-white shadow-sm rounded-lg p-6 mb-6 border-2 border-blue-100">
            <h2 class="text-xl font-bold text-blue-900 mb-4 border-b pb-2">1. Header (Hero) Image</h2>
            <form method="POST" action="{{ route('admin.about.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label for="hero_image" class="block text-sm font-bold text-gray-700 mb-1">
                        Main Header Image
                    </label>
                    <p class="text-xs text-gray-500 mb-3">This is the large image displayed at the very top of the home page.</p>
                    
                    @if($about && $about->hero_image_url)
                        <div class="mb-4">
                            <p class="text-xs font-semibold text-gray-400 mb-1">Current Image:</p>
                            <img src="{{ $about->hero_image_url }}" alt="Hero Image" class="w-full max-w-sm h-auto rounded shadow-sm border">
                        </div>
                    @endif
                    
                    <div class="flex items-center space-x-2">
                        <input
                            type="file"
                            id="hero_image"
                            name="hero_image"
                            accept="image/*"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    @error('hero_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <h2 class="text-xl font-bold text-gray-900 mt-10 mb-4 border-b pb-2">2. About Section Text</h2>

                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Title
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $about->title ?? '') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >{{ old('description', $about->description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <button
                        type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm"
                    >
                        Save All Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

