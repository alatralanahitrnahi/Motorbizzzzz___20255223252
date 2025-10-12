@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Invoice {{ $invoice->invoice_number }}</h1>
        <div>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-secondary">Download PDF</a>
            @can('edit-invoices')
                @if($invoice->status !== 'paid')
                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Mark this invoice as paid?')">Mark as Paid</button>
                    </form>
                @endif
            @endcan
            <a href="{{ route('invoices.index') }}" class="btn btn-primary">Back to Invoices</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Invoice Details</h5>
                            <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                            <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}</p>
                            @if($invoice->due_date)
                                <p><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</p>
                            @endif
                            @if($invoice->workOrder)
                                <p><strong>Work Order:</strong> 
                                    <a href="{{ route('work-orders.show', $invoice->workOrder) }}">
                                        {{ $invoice->workOrder->order_number }}
                                    </a>
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5>Customer Details</h5>
                            <p><strong>Name:</strong> {{ $invoice->customer_name }}</p>
                            @if($invoice->customer_email)
                                <p><strong>Email:</strong> {{ $invoice->customer_email }}</p>
                            @endif
                            @if($invoice->customer_phone)
                                <p><strong>Phone:</strong> {{ $invoice->customer_phone }}</p>
                            @endif
                            @if($invoice->customer_gstin)
                                <p><strong>GSTIN:</strong> {{ $invoice->customer_gstin }}</p>
                            @endif
                            <p><strong>Address:</strong><br>{{ $invoice->customer_address }}</p>
                        </div>
                    </div>

                    <h5>Invoice Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                    <th>Tax Rate</th>
                                    <th>Tax Amount</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                    <tr>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                        <td>₹{{ number_format($item->subtotal, 2) }}</td>
                                        <td>{{ $item->tax_rate }}%</td>
                                        <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                                        <td>₹{{ number_format($item->total_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-right">Subtotal:</th>
                                    <th>₹{{ number_format($invoice->subtotal, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-right">Tax Amount:</th>
                                    <th>₹{{ number_format($invoice->tax_amount, 2) }}</th>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="6" class="text-right">Total Amount:</th>
                                    <th>₹{{ number_format($invoice->total_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($invoice->notes)
                        <div class="mt-4">
                            <h5>Notes</h5>
                            <p>{{ $invoice->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Invoice Status</h5>
                </div>
                <div class="card-body">
                    <p>
                        <span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }} badge-lg">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </p>
                    
                    @if($invoice->isOverdue())
                        <div class="alert alert-danger">
                            <strong>Overdue!</strong> This invoice was due on {{ $invoice->due_date->format('M d, Y') }}.
                        </div>
                    @endif

                    <div class="mt-3">
                        <small class="text-muted">
                            Created: {{ $invoice->created_at->format('M d, Y g:i A') }}<br>
                            Updated: {{ $invoice->updated_at->format('M d, Y g:i A') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection