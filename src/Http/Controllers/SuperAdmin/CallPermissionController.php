<?php

namespace Towoju\One2OneCalls\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CallPermissionController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-call-permissions');
        $users = \App\Models\User::query()
            ->select('id','name','email','can_initiate_call')
            ->orderBy('name')
            ->paginate(20);

        return view('one2one-calls::permissions', compact('users'));
    }

    public function toggle(Request $request, int $userId)
    {
        Gate::authorize('manage-call-permissions');

        $user = \App\Models\User::findOrFail($userId);
        $user->can_initiate_call = ! (bool) $user->can_initiate_call;
        $user->save();

        return redirect()->back()->with('status', 'Updated permission for ' . $user->email);
    }
}
