<!-- @extends('layouts.app')

@section('content')
@endsection -->
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Call Permission Manager</h1>
    @if(session('status'))
        <div class="p-3 rounded bg-green-100 mb-3">{{ session('status') }}</div>
    @endif
    <table class="min-w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 text-left border">ID</th>
                <th class="p-2 text-left border">Name</th>
                <th class="p-2 text-left border">Email</th>
                <th class="p-2 text-left border">Can Initiate?</th>
                <th class="p-2 text-left border">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td class="p-2 border">{{ $user->id }}</td>
                <td class="p-2 border">{{ $user->name }}</td>
                <td class="p-2 border">{{ $user->email }}</td>
                <td class="p-2 border">
                    @if($user->can_initiate_call)
                        <span class="px-2 py-1 text-xs rounded bg-green-200">YES</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-red-200">NO</span>
                    @endif
                </td>
                <td class="p-2 border">
                    <form method="POST" action="{{ route('one2one.calls.permissions.toggle', $user->id) }}">
                        @csrf
                        @method('PATCH')
                        <button class="px-3 py-1 rounded bg-indigo-600 text-white">Toggle</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>