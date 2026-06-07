<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->latest('is_default')->latest()->get();

        return view('customer.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['user_id'] = $request->user()->id;
        $validated['is_default'] = $request->boolean('is_default');

        if ($validated['is_default']) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $request->user()->addresses()->create($validated);

        return back()->with('success', 'Đã thêm địa chỉ nhận hàng.');
    }

    public function update(Request $request, UserAddress $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $validated = $this->validated($request);
        $validated['is_default'] = $request->boolean('is_default');

        if ($validated['is_default']) {
            $request->user()->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return back()->with('success', 'Đã cập nhật địa chỉ.');
    }

    public function destroy(Request $request, UserAddress $address)
    {
        abort_unless($address->user_id === $request->user()->id, 403);

        $address->delete();

        return back()->with('success', 'Đã xóa địa chỉ.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'address_line' => ['required', 'string', 'max:255'],
            'ward' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
        ]);
    }
}
