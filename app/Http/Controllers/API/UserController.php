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
            if (!Gate::allows('isAdmin')) {
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
        return User::where('type', '!=', 'admin')->latest()->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'             => 'required|string|max:191',
            'surname'             => 'required|string|max:191',
            'username'             => 'required|string|max:191|unique:users',
            'password'             => 'required|string|min:8',
            'type'             => 'required|string|max:191',
        ]);

        return User::create([
            'matricule'=> $request['matricule'],
            'name'=> $request ['name'],
            'surname'=> $request['surname'],
            'username'=> $request['username'],
            'password'=> Hash::make($request['password']),
            'type'=> $request['type'],
      ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $this->validate($request,[
            'name'             => 'required|string|max:191',
            'surname'             => 'required|string|max:191',
            'username'             => 'required|string|max:191|unique:users,username,'.$user->id,
            'password'             => 'required|string|min:8',
            'type'             => 'required|string|max:191',
        ]);
        $user->update([
            'matricule'=> $request['matricule'],
            'name'=>$request['name'],
            'surname'=>$request['surname'],
            'username'=>$request['username'],
            'type'=>$request['type'],
            'password'=>Hash::make($request['password']),
            ]);
        return ['message' => 'Updated the user info'];
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
