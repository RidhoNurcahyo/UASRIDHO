<?php



namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    // Tampilkan semua customer
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers, 200);
    }

    // Simpan customer baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
        ]);

        $validated['customer_id'] = (string) Str::uuid();
        $validated['password'] = bcrypt($validated['password']);

        $customer = Customer::create($validated);

        return response()->json($customer, 201);
    }

    // Tampilkan customer berdasarkan route model binding
    public function show(Customer $customer)
    {
        return response()->json($customer, 200);
    }

    // Update data customer
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|unique:customers,email,' . $customer->customer_id . ',customer_id',
            'password' => 'sometimes|required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $customer->update($validated);

        return response()->json($customer, 200);
    }

    // Hapus customer
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted'], 200);
    }
}
