<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    /**
     * Tampilkan semua customer.
     */
    public function index(): JsonResponse
    {
        $customers = Customer::all();
        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ], 200);
    }

    /**
     * Simpan customer baru.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:customers,email',
                'password' => 'required|string|min:8|max:255',
                'phone'    => 'nullable|string|max:20',
                'address'  => 'nullable|string|max:500',
            ]);

            $validated['customer_id'] = (string) Str::uuid();
            $validated['password'] = Hash::make($validated['password']);

            $customer = Customer::create($validated);

            return response()->json([
                'status' => 'success',
                'data' => $customer,
                'message' => 'Customer created successfully',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan customer berdasarkan ID.
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $customer,
        ], 200);
    }

    /**
     * Update data customer.
     */
    public function update(Request $request, Customer $customer): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name'     => 'sometimes|string|max:255',
                'email'    => 'sometimes|email|unique:customers,email,' . $customer->customer_id . ',customer_id',
                'password' => 'sometimes|string|min:8|max:255',
                'phone'    => 'nullable|string|max:20',
                'address'  => 'nullable|string|max:500',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $customer->update($validated);

            return response()->json([
                'status' => 'success',
                'data' => $customer,
                'message' => 'Customer updated successfully',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus customer.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        try {
            $customer->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Customer deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}