@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Invoices</h1>
        @can('create-invoices')
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">Create Invoice</a>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Work Order</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->customer_name }}</td>
                                <td>
                                    @if($invoice->workOrder)
                                        <a href="{{ route('work-orders.show', $invoice->workOrder) }}">
                                            {{ $invoice->workOrder->order_number }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                <td>₹{{ number_format($invoice->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-outline-secondary">PDF</a>
                                        @can('edit-invoices')
                                            @if($invoice->status !== 'paid')
                                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection