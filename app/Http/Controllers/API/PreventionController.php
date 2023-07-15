<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Prevention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class PreventionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('isManager') && !Gate::allows('isWorker')){
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
        return Prevention::latest()->paginate(5);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'task'             => 'required|string|max:128',
            'start_date'             => 'required|date',
            'end_date'             => 'required|date',
            'workers'             => 'required|array',
            'machine'             => 'required|string|max:128',
            'parts'             => 'required|string|max:128',
            'priority'             => 'required|string|max:128',
            'state'             => 'required|string|max:128'
        ]);
        $workers = json_encode($request->input('workers'));
        return Prevention::create([
            'task'=> $request['task'],
            'start_date'=> $request ['start_date'],
            'end_date'=> $request['end_date'],
            'workers' => $workers,
            'machine'=> $request['machine'],
            'parts'=> $request['parts'],
            'priority'=> $request ['priority'],
            'state'=> $request ['state']
      ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $query)
    {
        return Prevention::where(function ($queryBuilder) use ($query) {
            $queryBuilder->whereRaw('LOWER(task) LIKE ?', ['%' . strtolower($query) . '%'])
                            ->orWhereRaw('LOWER(workers) LIKE ?', ['%' . strtolower($query) . '%'])
                                ->orWhereRaw('LOWER(machine) LIKE ?', ['%' . strtolower($query) . '%']);
                                        
        })
        ->latest()
        ->paginate(5);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $prevention = Prevention::findOrFail($id);
        $this->validate($request, [
            'task'             => 'required|string|max:128',
            'start_date'             => 'required|date',
            'end_date'             => 'required|date',
            'workers'             => 'required|array',
            'machine'             => 'required|string|max:128',
            'parts'             => 'required|string|max:128',
            'priority'             => 'required|string|max:128',
            'state'             => 'required|string|max:128'
        ]);
        $data = [
            'task'=> $request['task'],
            'start_date'=> $request ['start_date'],
            'end_date'=> $request['end_date'],
            'workers'=> $request['workers'],
            'machine'=> $request['machine'],
            'parts'=> $request['parts'],
            'priority'=> $request ['priority'],
            'state'=> $request ['state']
        ];
        $prevention->update($data);
        return ['message' => 'Infos Updated'];
    }
    public function updatePreventionState(Request $request, string $id)
    {
        $prevention = Prevention::findOrFail($id);
        $prevention->state = $request->input('state');
        $prevention->save();
    
        return response()->json(['message' => 'Prevention state updated successfully']);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prevention = Prevention::findOrFail($id);
        $prevention->delete();
        return ['message' => 'Deleted'];
    }
}
