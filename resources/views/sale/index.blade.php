@extends('layout.master')
@section('title','Product List')
@push('plugin-styles')
  <link href="{{ asset('build/plugins/datatables.net-bs5/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endpush
@push('style')

@endpush

@section('content')

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                {{-- Header --}}
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Sales List</h6>
                    <a href="{{ route('sale.create-sale') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Create New Sale
                    </a>
                </div>

                {{-- Filter Section --}}
                <div class="card mb-3 p-3 border-light shadow-sm">
                    <form id="salesFilterForm" class="row g-3 align-items-end">

                        <div class="col-md-3">
                            <label for="filterCustomer" class="form-label">Customer Name</label>
                            <select id="filterCustomer" name="customer" class="form-select"></select>
                        </div>

                        <div class="col-md-3">
                            <label for="filterProduct" class="form-label">Product Name</label>
                            <select id="filterProduct" name="product" class="form-select"></select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date Range</label>
                            <div class="input-group">
                                <input type="date" id="filterStartDate" name="start_date" class="form-control">
                                <span class="input-group-text">to</span>
                                <input type="date" id="filterEndDate" name="end_date" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                            <button type="button" id="resetFilter" class="btn btn-secondary w-100 mt-1">Reset</button>
                        </div>

                    </form>

                </div>

                {{-- Product Table --}}
                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                        <tr>
                            <th>#Id</th>
                            <th>Created By</th>
                            <th>Customer</th>
                            <th>Sale Date</th>
                            <th>Subtotal</th>
                            <th>Total Discount</th>
                            <th>Total Amount</th>
                            <th>Status</th>
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
            let table = $('#dataTableExample').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                order: [[0, 'desc']],
                ajax: {
                    url: '{{ route("sale.sale-list") }}',
                    type: 'GET',
                    data: function (d) {
                        d.customer = $('#filterCustomer').val();
                        d.product = $('#filterProduct').val();
                        d.start_date = $('#filterStartDate').val();
                        d.end_date = $('#filterEndDate').val();
                    }
                },
                "columns": [
                    {data: 'id', name: 'id'},
                    {data: 'user_id', name: 'user_id'},
                    {data: 'customer_id', name: 'customer_id'},
                    {data: 'sale_date', name: 'sale_date'},
                    {data: 'subtotal', name: 'subtotal'},
                    {data: 'discount_total', name: 'discount_total'},
                    {data: 'total_amount', name: 'total_amount'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                "columnDefs": [
                    {
                        targets: 1,
                        render: function (data, type, row, meta) {
                            return row.created_by.name
                        }
                    },
                    {
                        targets: 2,
                        render: function (data, type, row, meta) {
                            return row.customer.name
                        }
                    },
                    {
                        targets: 3,
                        render: function (data, type, row, meta) {
                            let dateTime = '';
                            if (type === 'display') {
                                dateTime = moment(row.sale_date).format('DD-MMM-YYYY');
                            }
                            return dateTime;
                        }
                    },
                    {
                        targets: 5,
                        render: function (data, type, row, meta) {
                            return row.discount_total + ' BDT'
                        }
                    },
                    {
                        targets: 6,
                        render: function (data, type, row, meta) {
                            return row.total_amount + ' BDT'
                        }
                    },
                    {
                        targets: 7,
                        render: function (data, type, row, meta) {
                            if(row.deleted_at){
                                return '<span class="badge bg-danger">Deleted</span>';
                            } else {
                                return '<span class="badge bg-success">Active</span>';
                            }
                        }
                    },
                ]
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
                    } else if (type === 'passing-parameter-execute-restore') {
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

            $(document).ready(function () {

                $('#filterCustomer').select2({
                    placeholder: 'Search by customer',
                    allowClear: true,
                    dropdownParent: $('.card-body'),
                    ajax: {
                        url: `{{route('sale.search-customers')}}`,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {search: params.term};
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(function (customer) {
                                    return {id: customer.id, text: customer.name};
                                })
                            };
                        },
                        cache: true
                    }
                });

                $('#filterProduct').select2({
                    placeholder: 'Search by product',
                    allowClear: true,
                    dropdownParent: $('#filterProduct').parent(),
                    ajax: {
                        url: `{{ route('sale.search-products') }}`,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {q: params.term};
                        },
                        processResults: function (data) {
                            return {results: data};
                        },
                        cache: true
                    }
                });


                $('#filterCustomer, #filterProduct').on('change', function () {
                    $('#dataTableExample').DataTable().ajax.reload();
                });
            });

            $('#salesFilterForm').on('submit', function (e) {
                e.preventDefault();
                table.ajax.reload();
            });

            $('#resetFilter').on('click', function () {
                $('#salesFilterForm')[0].reset();
                $('#filterCustomer').val(null).trigger('change');
                $('#filterProduct').val(null).trigger('change');
                table.ajax.reload();
            });

        });

    </script>
@endpush
