<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::latest()->paginate(5);
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
