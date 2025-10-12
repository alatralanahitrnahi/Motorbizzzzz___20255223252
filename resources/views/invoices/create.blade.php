@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create Invoice</h1>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back to Invoices</a>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        
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
                                    <label for="work_order_id">Work Order (Optional)</label>
                                    <select name="work_order_id" id="work_order_id" class="form-control">
                                        <option value="">Select Work Order</option>
                                        @foreach($workOrders as $workOrder)
                                            <option value="{{ $workOrder->id }}">{{ $workOrder->order_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="invoice_date">Invoice Date</label>
                                    <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_name">Customer Name</label>
                                    <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_email">Customer Email</label>
                                    <input type="email" name="customer_email" id="customer_email" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_phone">Customer Phone</label>
                                    <input type="text" name="customer_phone" id="customer_phone" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_gstin">Customer GSTIN</label>
                                    <input type="text" name="customer_gstin" id="customer_gstin" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="customer_address">Customer Address</label>
                            <textarea name="customer_address" id="customer_address" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="due_date">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Invoice Items</h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">Add Item</button>
                    </div>
                    <div class="card-body">
                        <div id="items-container">
                            <div class="item-row">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" name="items[0][description]" placeholder="Description" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="items[0][quantity]" placeholder="Qty" class="form-control" step="0.01" min="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="items[0][unit_price]" placeholder="Unit Price" class="form-control" step="0.01" min="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="items[0][tax_rate]" placeholder="Tax %" class="form-control" step="0.01" min="0" max="100" value="18" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">Remove</button>
                                    </div>
                                </div>
                            </div>
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
                        <button type="submit" class="btn btn-primary btn-block">Create Invoice</button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-block">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemIndex = 1;

function addItem() {
    const container = document.getElementById('items-container');
    const newItem = document.createElement('div');
    newItem.className = 'item-row mt-3';
    newItem.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="items[${itemIndex}][description]" placeholder="Description" class="form-control" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemIndex}][quantity]" placeholder="Qty" class="form-control" step="0.01" min="0.01" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemIndex}][unit_price]" placeholder="Unit Price" class="form-control" step="0.01" min="0" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemIndex}][tax_rate]" placeholder="Tax %" class="form-control" step="0.01" min="0" max="100" value="18" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">Remove</button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    itemIndex++;
}

function removeItem(button) {
    const itemRow = button.closest('.item-row');
    if (document.querySelectorAll('.item-row').length > 1) {
        itemRow.remove();
    }
}
</script>
@endsection