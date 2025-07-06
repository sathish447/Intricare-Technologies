<table class="min-w-full bg-white border">
    <thead>
        <tr>
            <th class="px-4 py-2 border">#</th>
            <th class="px-4 py-2 border">Name</th>
            <th class="px-4 py-2 border">Email</th>
            <th class="px-4 py-2 border">Phone</th>
            <th class="px-4 py-2 border">Gender</th>
            <th class="px-4 py-2 border">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contacts as $c)
            <tr class="text-center">
                <td class="border px-2 py-1">{{ $loop->iteration + ($contacts->currentPage()-1)*$contacts->perPage() }}</td>
                <td class="border px-2 py-1">{{ $c->name }}</td>
                <td class="border px-2 py-1">{{ $c->email }}</td>
                <td class="border px-2 py-1">{{ $c->phone }}</td>
                <td class="border px-2 py-1">{{ $c->gender }}</td>
                <td class="border px-2 py-1 space-x-2">
                    <button class="bg-yellow-500 text-white px-2 py-1 rounded btn-edit" 
                            data-id="{{ $c->id }}">Edit</button>
                    <button class="bg-red-600 text-white px-2 py-1 rounded btn-delete" 
                            data-url="{{ route('contacts.destroy',$c) }}">Delete</button>
                    <button class="bg-indigo-600 text-white px-2 py-1 rounded btn-merge" 
                            data-id="{{ $c->id }}">Merge</button>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center p-4">No contacts found.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="mt-4">{{ $contacts->links() }}</div>
