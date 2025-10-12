<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('business_id', auth()->user()->business_id)->latest()->get();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        $validated['business_id'] = auth()->user()->business_id;
        $validated['status'] = 'pending';
        $validated['issued_date'] = now();

        Invoice::create($validated);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully!');
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
        return redirect()->back()->with('success', 'Invoice marked as paid!');
    }
}