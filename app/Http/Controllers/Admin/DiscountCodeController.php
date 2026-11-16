<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountCodeController extends Controller
{
    public function index()
    {
        $codes = DiscountCode::latest()->paginate(20);
        return view('admin.discounts.index', compact('codes'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        DiscountCode::create($data);
        return redirect()->route('admin.discounts.index')->with('success', 'Kod rabatowy dodany!');
    }

    public function edit(DiscountCode $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, DiscountCode $discount)
    {
        $data = $this->validateData($request, $discount->id);
        $discount->update($data);
        return redirect()->route('admin.discounts.index')->with('success', 'Kod rabatowy zaktualizowany!');
    }

    public function destroy(DiscountCode $discount)
    {
        $discount->delete();
        return redirect()->route('admin.discounts.index')->with('success', 'Kod rabatowy usunięty!');
    }

    /**
     * Wspólna walidacja dla store i update.
     * $ignoreId — przy edycji pomijamy własny rekord przy sprawdzaniu unikalności kodu.
     */
    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('discount_codes', 'code')->ignore($ignoreId)],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => ['required', 'integer', 'min:1', $request->input('type') === 'percent' ? 'max:100' : 'max:100000000'],
            'min_cart_value' => ['nullable', 'integer', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ]);

        // normalizacja kodu (SER10, nie "ser10 ") + checkbox na bool
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');
        if (!empty($validated['expires_at'])) {
            $validated['expires_at'] = \Carbon\Carbon::parse($validated['expires_at'])->endOfDay();// robi, ze kod jest do konca dnia, nie do 00:00 poczatku dnia
        }

        return $validated;
    }
}
