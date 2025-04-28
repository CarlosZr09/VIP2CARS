<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Document;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['documents'] = Document::where('status',1)->get();
        return view('vehicles',$data);
    }

    public function json(){
        return Vehicle::with('customer')->get()->map(fn($v)=>[
        'id'        => $v->id,
        'plate'     => $v->plate,
        'brand'     => $v->brand,
        'model'     => $v->model,
        'year'      => $v->year_of_manufacture,
        'customer'     => $v->customer
        ]);
    }

    public function store(Request $request){
        $rucId = Document::where('short_name','RUC')->value('id');

        $data = $request->validate([
        // vehículo
        'plate'                => 'required|string|unique:vehicles,plate',
        'brand'                => 'required|string',
        'model'                => 'required|string',
        'year_of_manufacture'   => 'required|digits:4|integer',

        // cliente
        'first_name'           => 'required|string',
        'last_name'            => 'required|string',
        'document_type'          => 'required|exists:documents,id',
        'document_number'      => 'required|string|unique:customers,document_number',
        'email'                => 'required|email|unique:customers,email',
        'phone'                => 'required|string',
        'business_name'        => 'nullable|string',
        ]);

        if ($request->filled('customer_id')) {
            $customer = Customer::find($request->customer_id);
            $customer->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'document_id' => $request->document_type,
                'document_number' => $request->document_number,
                'email' => $request->email,
                'phone' => $request->phone,
                'business_name' => $request->business_name,
            ]);
        } else {
            $customer = Customer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'document_id' => $request->document_type,
                'document_number' => $request->document_number,
                'email' => $request->email,
                'phone' => $request->phone,
                'business_name' => $request->business_name,
            ]);
        }

        $vehicle = Vehicle::create([
        'plate'                => $data['plate'],
        'brand'                => $data['brand'],
        'model'                => $data['model'],
        'year_of_manufacture'   => $data['year_of_manufacture'],
        'customer_id'          => $customer->id,
        ]);

        return response()->json($vehicle->load('customer'), 201);
    }

    public function update(Request $request, $id){
        $vehicle = Vehicle::findOrFail($id);
        $customer = $vehicle->customer;

        $data = $request->validate([
        'plate'                => ['required','string',Rule::unique('vehicles','plate')->ignore($id)],
        'brand'                => 'required|string',
        'model'                => 'required|string',
        'year_of_manufacture'   => 'required|digits:4|integer',
        'first_name'           => 'required|string',
        'last_name'            => 'required|string',
        'document_type'          => 'required|exists:documents,id',
        'document_number'      => ['required','string',Rule::unique('customers','document_number')->ignore($customer->id)],
        'email'                => ['required','email',Rule::unique('customers','email')->ignore($customer->id)],
        'phone'                => 'required|string',
        'business_name'        => 'nullable|string',
        ]);

        $customer->update($data);

        $vehicle->update($data);

        return response()->json($vehicle->load('customer'));
    }

    public function show($id){
        $vehicle = Vehicle::where('id',$id)->with('customer')->first();
        return response()->json($vehicle);
    }

    public function destroy($id){
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();
        return response()->json(['message' => 'Vehículo eliminado correctamente']);
    }
}
