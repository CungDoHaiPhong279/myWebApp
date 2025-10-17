<x-app-layout>
    <div class="max-w-2xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-gray-100 rounded-lg shadow-md">
            <div class="bg-gray-200 px-4 py-3 text-lg font-semibold rounded-t-lg">
                {{ $note->title }}
            </div>
            <div class="px-4 py-5">
                <p class="text-gray-800">{{ $note->text }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
