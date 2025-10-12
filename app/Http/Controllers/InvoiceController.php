<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Http\Requests\InvoiceRequest;
// use Barryvdh\DomPDF\Facade\Pdf; // Uncomment when DomPDF is installed

class InvoiceController extends Controller
{
    public function index()
    {
        $this->authorize('view-invoices');
        
        $invoices = Invoice::with('workOrder')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $this->authorize('create-invoices');
        
        $workOrders = WorkOrder::where('status', 'completed')
            ->whereDoesntHave('invoice')
            ->get();
            
        return view('invoices.create', compact('workOrders'));
    }

    public function store(InvoiceRequest $request)
    {
        $this->authorize('create-invoices');
        
        // Validation handled by InvoiceRequest

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'work_order_id' => $request->work_order_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'customer_gstin' => $request->customer_gstin,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total_amount' => 0,
        ]);

        $subtotal = 0;
        $totalTax = 0;

        foreach ($request->items as $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $taxAmount = ($itemSubtotal * $item['tax_rate']) / 100;
            $itemTotal = $itemSubtotal + $taxAmount;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'],
                'tax_amount' => $taxAmount,
                'total_amount' => $itemTotal,
            ]);

            $subtotal += $itemSubtotal;
            $totalTax += $taxAmount;
        }

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'total_amount' => $subtotal + $totalTax,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view-invoices');
        
        $invoice->load('items', 'workOrder');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('edit-invoices');
        
        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'Cannot edit paid invoices.');
        }
        
        $invoice->load('items');
        $workOrders = WorkOrder::where('status', 'completed')->get();
        
        return view('invoices.edit', compact('invoice', 'workOrders'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('edit-invoices');
        
        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'Cannot edit paid invoices.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'required|string',
            'customer_gstin' => 'nullable|string|max:15',
            'due_date' => 'nullable|date|after:invoice_date',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
        ]);

        $invoice->update($request->only([
            'customer_name', 'customer_email', 'customer_phone', 
            'customer_address', 'customer_gstin', 'due_date', 'status'
        ]));

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete-invoices');
        
        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'Cannot delete paid invoices.');
        }
        
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $this->authorize('view-invoices');
        
        $invoice->load('items', 'workOrder');
        
        // For now, return the PDF view directly until DomPDF is installed
        return view('invoices.pdf', compact('invoice'));
        
        // Uncomment when DomPDF is installed:
        // $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        // return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function markAsPaid(Invoice $invoice)
    {
        $this->authorize('edit-invoices');
        
        $invoice->update(['status' => 'paid']);
        
        return redirect()->back()->with('success', 'Invoice marked as paid.');
    }
}