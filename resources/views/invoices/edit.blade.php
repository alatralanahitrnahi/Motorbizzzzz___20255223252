@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Invoice {{ $invoice->invoice_number }}</h1>
        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">Back to Invoice</a>
    </div>

    <form action="{{ route('invoices.update', $invoice) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Invoice Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_name">Customer Name</label>
                                    <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ $invoice->customer_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_email">Customer Email</label>
                                    <input type="email" name="customer_email" id="customer_email" class="form-control" value="{{ $invoice->customer_email }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_phone">Customer Phone</label>
                                    <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="{{ $invoice->customer_phone }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_gstin">Customer GSTIN</label>
                                    <input type="text" name="customer_gstin" id="customer_gstin" class="form-control" value="{{ $invoice->customer_gstin }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="customer_address">Customer Address</label>
                            <textarea name="customer_address" id="customer_address" class="form-control" rows="3" required>{{ $invoice->customer_address }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control" value="{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="draft" {{ $invoice->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="sent" {{ $invoice->status === 'sent' ? 'selected' : '' }}>Sent</option>
                                        <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="overdue" {{ $invoice->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                        <option value="cancelled" {{ $invoice->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Invoice Items (Read Only)</h5>
                        <small class="text-muted">To modify items, create a new invoice</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Tax Rate</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->items as $item)
                                        <tr>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ $item->tax_rate }}%</td>
                                            <td>₹{{ number_format($item->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-success">
                                        <th colspan="4" class="text-right">Total Amount:</th>
                                        <th>₹{{ number_format($invoice->total_amount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Actions</h5>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block">Update Invoice</button>
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary btn-block">Cancel</a>
                        
                        @can('delete-invoices')
                            @if($invoice->status !== 'paid')
                                <hr>
                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-block">Delete Invoice</button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection