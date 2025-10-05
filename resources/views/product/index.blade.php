@extends('layout.master')
@section('title','Product List')
@push('plugin-styles')
  <link href="{{ asset('build/plugins/datatables.net-bs5/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endpush
@push('style')
    <style>
        #productStatusTabs .status-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background: #fff;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.2s ease-in-out;
            border: 1px solid #e5e7eb;
            color: #000;
            text-align: center;
        }

        #productStatusTabs .status-card i {
            font-size: 22px;
            margin-bottom: 6px;
        }

        #productStatusTabs .status-card .label {
            font-weight: 600;
            font-size: 14px;
        }

        #productStatusTabs .status-card .count {
            margin-top: 6px;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 10px;
        }

        #productStatusTabs .status-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        #productStatusTabs .status-card.active {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }

        #productStatusTabs .status-card.active i,
        #productStatusTabs .status-card.active .label,
        #productStatusTabs .status-card.active .count {
            color: #fff !important;
        }

    </style>
@endpush

@section('content')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                {{-- Header --}}
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Product List</h6>
                    <a href="{{ route('product.create-product') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Create New Product
                    </a>
                </div>

                <div class="mb-4 p-3 border rounded bg-light">
                    <div class="row g-3" id="productStatusTabs">

                        <div class="col-4 col-sm-3 col-md-4">
                            <a class="status-card active" data-status="all" href="{{route('product.product-list',['type'=>'all'])}}">
                                <i class="fa-solid fa-layer-group"></i>
                                <span class="label">All</span>
                                <span class="badge count bg-primary">{{ $counts['total'] ?? 0 }}</span>
                            </a>
                        </div>

                        <div class="col-4 col-sm-3 col-md-4">
                            <a class="status-card" data-status="active" href="{{route('product.product-list',['type'=>'active'])}}">
                                <i class="fa-solid fa-box-open text-primary"></i>
                                <span class="label">Active</span>
                                <span class="badge count bg-success">{{ $counts['active'] ?? 0 }}</span>
                            </a>
                        </div>

                        <div class="col-4 col-sm-3 col-md-4">
                            <a class="status-card" data-status="inactive" href="{{route('product.product-list',['type'=>'inactive'])}}">
                                <i class="fa-solid fa-pause-circle text-secondary"></i>
                                <span class="label">Inactive</span>
                                <span class="badge count bg-secondary">{{ $counts['inactive'] ?? 0 }}</span>
                            </a>
                        </div>

                        <div class="col-4 col-sm-3 col-md-4">
                            <a class="status-card" data-status="out_of_stock" href="{{route('product.product-list',['type'=>'out-of-stock'])}}">
                                <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                <span class="label">Out of Stock</span>
                                <span class="badge count bg-warning text-dark">{{ $counts['out_of_stock'] ?? 0 }}</span>
                            </a>
                        </div>

                        <div class="col-4 col-sm-3 col-md-4">
                            <a class="status-card" data-status="trashed" href="{{route('product.product-list',['type'=>'trash'])}}">
                                <i class="fa-solid fa-trash text-danger"></i>
                                <span class="label">Trashed</span>
                                <span class="badge count bg-danger">{{ $counts['trashed'] ?? 0 }}</span>
                            </a>
                        </div>

                    </div>
                </div>


                {{-- Product Table --}}
                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                        <tr>
                            <th>#Id</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Sku</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('build/plugins/datatables.net/dataTables.min.js') }}"></script>
  <script src="{{ asset('build/plugins/datatables.net-bs5/dataTables.bootstrap5.min.js') }}"></script>
  <script src="{{asset('js/moment.min.js')}}"></script>
@endpush

@push('custom-scripts')

    <script>
        $(document).ready(function () {
            $('#dataTableExample').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [[0, 'desc']],
                ajax: {
                    url: '{{ route("product.product-list") }}',
                    type: 'GET',
                    data:{
                        'type':`{{request()->get('type') ?? 'all'}}`
                    }
                },
                "columns": [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'image', name: 'image'},
                    {data: 'sku', name: 'sku'},
                    {data: 'price', name: 'price'},
                    {data: 'quantity', name: 'quantity'},
                    {data: 'stock_status', name: 'stock_status',orderable: false, searchable: false},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'created_at', name: 'created_at', orderable: true, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "columnDefs": [
                    {
                        targets: 1,
                        render: function (data, type, row, meta) {
                            return row.name
                        }
                    },
                    {
                        targets: 4,
                        render: function (data, type, row, meta) {
                            return row.price + " BDT"
                        }
                    },
                    {
                        targets: 6,
                        render: function (data, type, row, meta) {
                            return row.stock_status
                        }
                    },
                    {
                        targets: 7,
                        render: function (data, type, row, meta) {
                            if(row.deleted_at){
                                return '<span class="badge bg-danger">Deleted</span>';
                            }else if (row.status == 1) {
                                return '<span class="badge bg-success">Active</span>';
                            } else {
                                return '<span class="badge bg-warning">Inactive</span>';
                            }
                        }
                    },
                    {
                        targets: 8,
                        render: function (data, type, row, meta) {
                            let dateTime = '';
                            if (type === 'display') {
                                dateTime = moment(row.created_at).format('DD-MMM-YYYY');
                            }
                            return dateTime;
                        }
                    },
                ]
            });
        });

        $(function () {
            showSwal = function (type, url) {
                'use strict';
                if (type === 'passing-parameter-execute-cancel') {
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger me-2'
                        },
                        buttonsStyling: false,
                    })

                    swalWithBootstrapButtons.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'me-2',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    })
                        .then((result) => {
                            if (result.value) {

                                window.location.href = url;

                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                swalWithBootstrapButtons.fire(
                                    'Cancelled',
                                    'Your imaginary file is safe :)',
                                    'error'
                                )
                            }
                        })
                } else if(type === 'passing-parameter-execute-restore'){
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-danger me-2'
                        },
                        buttonsStyling: false,
                    })

                    swalWithBootstrapButtons.fire({
                        title: 'Are you sure?',
                        text: "You want to restore this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonClass: 'me-2',
                        confirmButtonText: 'Yes, restore it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    })
                        .then((result) => {
                            if (result.value) {

                                window.location.href = url;

                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                swalWithBootstrapButtons.fire(
                                    'Cancelled',
                                    'Your imaginary file is safe :)',
                                    'error'
                                )
                            }
                        })
                }
            }
        });

    </script>
@endpush
