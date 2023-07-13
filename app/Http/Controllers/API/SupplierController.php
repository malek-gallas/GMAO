<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
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
        return Supplier::latest()->paginate(5);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'             => 'required|string|max:128',
            'address'             => 'required|string|max:128',
            'telephone'             => 'required|numeric|max:9999999999999999',
            'fax'             => 'required|numeric|max:9999999999999999',
            'email'             => 'required|string|max:128|unique:suppliers',
        ]);
        return Supplier::create([
            'name'=> $request['name'],
            'address'=> $request ['address'],
            'telephone'=> $request['telephone'],
            'fax'=> $request['fax'],
            'email'=> $request['email'],
      ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $query)
    {
        return Supplier::where(function ($queryBuilder) use ($query) {
            $queryBuilder->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
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
        $supplier = Supplier::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:128',
            'address' => 'required|string|max:128',
            'telephone' => 'required|numeric|max:9999999999999999',
            'fax' => 'required|numeric|max:9999999999999999',
            'email' => 'required|string|max:128|unique:suppliers,email,'.$supplier->id,
        ]);
        $data = [
            'name' => $request['name'],
            'address' => $request['address'],
            'telephone' => $request['telephone'],
            'fax' => $request['fax'],
            'email' => $request['email'],
        ];
        $supplier->update($data);
        return ['message' => 'Supplier Infos Updated'];
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return ['message' => 'Supplier Deleted'];
    }
}
