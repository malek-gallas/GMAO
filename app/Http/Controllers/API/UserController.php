<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('isAdmin') && !Gate::allows('isManager') && !Gate::allows('isWorker')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::where('role', '!=', 'Administrateur')->latest()->paginate(5);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'employee_id'             => 'required|string|max:8|unique:users',
            'first_name'             => 'required|string|max:128',
            'last_name'             => 'required|string|max:128',
            'role'             => 'required|string|max:128',
            'email'             => 'required|string|max:128|unique:users',
            'password'             => 'required|string|min:8',
        ]);
        return User::create([
            'employee_id'=> $request['employee_id'],
            'first_name'=> $request ['first_name'],
            'last_name'=> $request['last_name'],
            'role'=> $request['role'],
            'email'=> $request['email'],
            'password'=> Hash::make($request['password']),   
      ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $query)
    {
        return User::where('role', '!=', 'Administrateur')
        ->where(function ($queryBuilder) use ($query) {
            $queryBuilder->whereRaw('LOWER(employee_id) LIKE ?', ['%' . strtolower($query) . '%'])
                            ->orWhereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($query) . '%'])
                                ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($query) . '%'])
                                    ->orWhereRaw('LOWER(role) LIKE ?', ['%' . strtolower($query) . '%'])
                                        ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($query) . '%']);
                                        
        })
        ->latest()
        ->paginate(5);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
    
        $this->validate($request, [
            'employee_id' => 'required|string|max:8|unique:users,employee_id,'.$user->id,
            'first_name' => 'required|string|max:128',
            'last_name' => 'required|string|max:128',
            'role' => 'required|string|max:128',
            'email' => 'required|string|max:128|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
        ]);
        $data = [
            'employee_id' => $request['employee_id'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'role' => $request['role'],
            'email' => $request['email'],
        ];
        // Only update the password if it is provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request['password']);
        }
    
        $user->update($data);
    
        return ['message' => 'User Infos Updated'];
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return ['message' => 'User Deleted'];
    }
}