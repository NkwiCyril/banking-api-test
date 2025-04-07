<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerController extends Controller
{
    protected $relationships = [
        'bankAccounts',
    ];

    public function index(): JsonResource|JsonResponse
    {
        $customers = Customer::with($this->relationships)->latest()->get();

        return CustomerResource::collection($customers);
    }

    public function store(Request $request): JsonResource|JsonResponse
    {
        $data = $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email|unique:customers',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($data);

        return new CustomerResource($customer);
    }

    public function show(int $id): JsonResource|JsonResponse
    {
        $customer = Customer::with($this->relationships)->findOrFail($id);

        // Check if the customer exists
        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return new CustomerResource($customer);
    }

    public function update(Request $request, int $id): JsonResource|JsonResponse
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'full_name' => 'string',
            'email' => 'email|unique:customers,email,'.$customer->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $customer->update($data);

        return new CustomerResource($customer);
    }

    public function destroy(int $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);

        // Check if the customer exists
        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully'], 200);
    }
}
