<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    protected $relationships = [
        'bankAccounts',
    ];

    public function index(): JsonResource|JsonResponse
    {
        $customers = Customer::with($this->relationships)->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Customers retrieved successfully!',
            'statusCode' => 200,
            'data' => CustomerResource::collection($customers),
        ]);
    }

    public function store(Request $request): JsonResource|JsonResponse
    {
        try {
            $data = $request->validate([
                'full_name' => 'required|string',
                'email' => 'required|email|unique:customers',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $customer = Customer::create($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Customer created successfully!',
                'statusCode' => 201,
                'data' => new CustomerResource($customer->load($this->relationships)),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResource|JsonResponse
    {
        $customer = Customer::with($this->relationships)->findOrFail($id);

        // Check if the customer exists
        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Customer retrieved successfully!',
            'statusCode' => 200,
            'data' => new CustomerResource($customer),
        ]);
    }

    public function update(Request $request, int $id): JsonResource|JsonResponse
    {
        $customer = Customer::find($id);

        // Check if the customer exists
        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $data = $request->validate([
            'full_name' => 'string',
            'email' => 'email|unique:customers,email,'.$customer->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $customer->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Customer updated successfully!',
                'statusCode' => 200,
                'data' => new CustomerResource($customer),
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update customer',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    public function destroy(int $id): JsonResponse
    {
        $customer = Customer::find($id);

        // Check if the customer exists
        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        try {

            $customer->delete();

            return response()->json([
                'status' => true,
                'message' => 'Customer deleted successfully!',
                'statusCode' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage(),
            ], 500);

        }
    }
}
