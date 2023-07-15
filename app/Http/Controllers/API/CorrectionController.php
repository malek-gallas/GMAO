<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Correction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class CorrectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('isManager') && !Gate::allows('isWorker')) {
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
        return Correction::latest()->paginate(5);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'type'             => 'required|string|max:128',
            'task'             => 'required|string|max:128',
            'start_date'             => 'required|date',
            'end_date'             => 'required|date',
            'workers'             => 'required|array',
            'machine'             => 'required|string|max:128',
            'parts'             => 'required|string|max:128',
            'priority'             => 'required|string|max:128',
            'state'             => 'required|string|max:128'
        ]);
        return Correction::create([
            'task'=> $request['task'],
            'start_date'=> $request ['start_date'],
            'end_date'=> $request['end_date'],
            'type'=> $request['type'],
            'workers'=> $request['workers'],
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
        return Correction::where(function ($queryBuilder) use ($query) {
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
        $correction = Correction::findOrFail($id);
        $this->validate($request, [
            'type'             => 'required|string|max:128',
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
            'type'=> $request['type'],
            'workers'=> $request['workers'],
            'machine'=> $request['machine'],
            'parts'=> $request['parts'],
            'priority'=> $request ['priority'],
            'state'=> $request ['state']
        ];
        $correction->update($data);
        return ['message' => 'Correction Infos Updated'];
    }
    public function updateCorrectionState(Request $request, string $id)
    {
        $correction = Correction::findOrFail($id);
        $correction->state = $request->input('state');
        $correction->save();
    
        return response()->json(['message' => 'Correction state updated successfully']);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $correction = Correction::findOrFail($id);
        $correction->delete();
        return ['message' => 'Correction Deleted'];
    }
}
