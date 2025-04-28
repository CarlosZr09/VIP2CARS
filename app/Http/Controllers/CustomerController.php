<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $data['documents'] = Document::where('status',1)->get();
        return view('customers',$data);
    }

    public function json()
    {
        $data = Customer::with('document')->get()->map(function($c){
            return [
                'id'              => $c->id,
                'first_name'      => $c->first_name,
                'last_name'       => $c->last_name,
                'document_number' => $c->document_number,
                'email'           => $c->email,
                'phone'           => $c->phone,
            ];
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'first_name'      => 'required|string',
            'last_name'       => 'required|string',
            'document_id'     => 'required|exists:documents,id',
            'document_number' => 'required|string|unique:customers,document_number',
            'email'           => 'required|email|unique:customers,email',
            'phone'           => 'required|string',
            'business_name'   => 'nullable|string',
        ]);

        $customer = Customer::create($data);

        return response()->json($customer, 201);
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'first_name'      => 'required|string',
            'last_name'       => 'required|string',
            'document_id'     => 'required|exists:documents,id',
            'document_number' => [
                'required',
                'string',
                Rule::unique('customers', 'document_number')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($id),
            ],
            'phone'          => 'required|string',
            'business_name'  => 'nullable|string', // <- Igual que en store, por defecto es nullable
        ]);

        $customer->update($data);

        return response()->json($customer);
    }

    public function destroy($id){
        $customer = Customer::findOrFail($id);

        $vehicles = $customer->vehicles;

        if ($vehicles->count() > 0) {
            foreach ($vehicles as $vehicle) {
                $vehicle->delete();
            }
        }

        $customer->delete();

        return response()->json(['message' => 'Cliente y vehículos eliminados correctamente'], 200);
    }

    public function search(Request $request){
        $type = $request->input('type');
        $number = $request->input('number');

        $cliente = Customer::where('document_id', $type)
                    ->where('document_number', $number)
                    ->first();

        if ($cliente) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $cliente->id,
                    'first_name' => $cliente->first_name,
                    'last_name' => $cliente->last_name,
                    'email' => $cliente->email,
                    'phone' => $cliente->phone,
                    'business_name' => $cliente->business_name,
                ]
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
}
