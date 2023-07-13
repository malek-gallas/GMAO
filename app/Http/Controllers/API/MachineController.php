<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class MachineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('isManager')) {
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
        return Machine::latest()->paginate(5);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'code'             => 'required|string|max:8',
            'name'             => 'required|string|max:128',
            'type'             => 'required|string|max:128',
            'serie'             => 'required|string|max:128',
            'section'             => 'required|string|max:128',
            'unit'             => 'required|string|max:128',
            'supplier'             => 'required|string|max:128',
            'purchase_date'             => 'required|date'
        ]);
        return Machine::create([
            'code'=> $request['code'],
            'name'=> $request ['name'],
            'type'=> $request['type'],
            'serie'=> $request['serie'],
            'section'=> $request['section'],
            'unit'=> $request['unit'],
            'supplier'=> $request ['supplier'],
            'purchase_date'=> $request['purchase_date']
      ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $query)
    {
        return Machine::where(function ($queryBuilder) use ($query) {
            $queryBuilder->whereRaw('LOWER(cde) LIKE ?', ['%' . strtolower($query) . '%'])
                            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%']);
                                        
        })
        ->latest()
        ->paginate(5);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $Machine = Machine::findOrFail($id);
        $this->validate($request, [
            'code'             => 'required|string|max:8',
            'name'             => 'required|string|max:128',
            'type'             => 'required|string|max:128',
            'serie'             => 'required|string|max:128',
            'section'             => 'required|string|max:128',
            'unit'             => 'required|string|max:128',
            'supplier'             => 'required|string|max:128',
            'purchase_date'             => 'required|date'
        ]);
        $data = [
            'code'=> $request['code'],
            'name'=> $request ['name'],
            'type'=> $request['type'],
            'serie'=> $request['serie'],
            'section'=> $request['section'],
            'ubit'=> $request['ubit'],
            'supplier'=> $request ['supplier'],
            'purchase_date'=> $request['purchase_date']
        ];
        $Machine->update($data);
        return ['message' => 'Machine Infos Updated'];
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Machine = Machine::findOrFail($id);
        $Machine->delete();
        return ['message' => 'Machine Deleted'];
    }
}
