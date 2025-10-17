<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">📋 Note Lists</h1>
            <a href="{{ route('notes.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                + Create
            </a>
        </div>

        {{-- Search Form --}}
        <form action="{{ route('notes.index') }}" method="GET" class="mb-6">
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="search"
                    class="w-full sm:flex-1 border border-gray-300 rounded px-4 py-2 shadow-sm focus:outline-none focus:ring focus:border-blue-400"
                    placeholder="🔍 Search notes by title or tag..." value="{{ request('search') }}">

                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                        Search
                    </button>

                    @if (request('search'))
                        <a href="{{ route('notes.index') }}"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-100 text-gray-700 text-left">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">Title</th>
                        <th class="p-3">Tags</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Created At</th>
                        <th class="p-3">Updated At</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notes as $note)
                        <tr class="border-t">
                            <td class="p-3">{{ $note->id }}</td>
                            <td class="p-3 font-semibold">{{ $note->title }}</td>
                            <td class="p-3 space-x-1">
                                @foreach ($note->tags as $tag)
                                    <span class="inline-block bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs mb-1">
                                        {{ $tag->tagName }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="p-3">
                                {{ $note->status?->name ?? 'No Status' }}
                            </td>
                            <td class="p-3">{{ $note->created_at }}</td>
                            <td class="p-3">{{ $note->updated_at }}</td>
                            <td class="p-3 flex flex-wrap gap-2">
                                <a href="{{ route('notes.note', ['id' => $note->id]) }}"
                                    class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition">
                                    See
                                </a>
                                <a href="{{ route('notes.edit', ['id' => $note->id]) }}"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded text-xs hover:bg-yellow-600 transition">
                                    Update
                                </a>
                                <form action="{{ route('notes.delete', ['id' => $note->id]) }}" method="POST"
                                    onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-6">No notes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6 flex justify-center">
            {{ $notes->links() }}
        </div>

    </div>
</x-app-layout>
