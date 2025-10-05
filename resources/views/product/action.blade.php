@extends('layout.master')
@section('title',$title)
@push('style')
    <style>
        .img-preview {
            max-width: 120px;
            max-height: 120px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    @php
        $isEdit = isset($product) && $product;
    @endphp
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{$isEdit ? 'Update New Product' : 'Add New Product'}}</h5>
                </div>
                <div class="card-body" id="productForm">
                    <form id="productFormElement" enctype="multipart/form-data" method="POST" action="{{ $isEdit ? route('product.update-product', $product->slug) : route('product.save-product') }}">
                        @csrf
                        @if($isEdit)
                            @method('PUT')
                        @endif

                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-md-6">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" id="productName" value="{{ old('name', $isEdit ? $product->name : '') }}" placeholder="Enter product name">
                                @error('name') <p class="text-danger">{{ $message }}</p> @enderror
                            </div>

                            <!-- SKU -->
                            <div class="col-md-6">
                                <label for="productSKU" class="form-label">SKU</label>
                                <input type="text" name="sku" class="form-control" id="productSKU" readonly value="{{ old('sku', $isEdit ? $product->sku : generate_sku()) }}" placeholder="Enter product SKU">
                                @error('sku') <p class="text-danger">{{ $message }}</p> @enderror
                            </div>

                            <!-- Image -->
                            <div class="col-md-6">
                                <label for="productImage" class="form-label">Product Image</label>
                                <input name="image" class="form-control" type="file" id="productImage" accept="image/*" onchange="previewImage(event)">
                                @if($isEdit && $product->image)
                                    <img id="imagePreview" src="{{ asset('storage/' . $product->image) }}" class="img-preview" alt="Preview">
                                @else
                                    <img id="imagePreview" class="img-preview d-none" alt="Preview">
                                @endif
                                @error('image') <p class="text-danger">{{ $message }}</p> @enderror
                            </div>

                            <!-- Price -->
                            <div class="col-md-6">
                                <label for="productPrice" class="form-label">Price</label>
                                <input name="price" type="number" class="form-control" id="productPrice" value="{{ old('price', $isEdit ? $product->price : '') }}" placeholder="Enter price" min="0" step="0.01">
                                @error('price') <p class="text-danger">{{ $message }}</p> @enderror
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-6">
                                <label for="productQuantity" class="form-label">Quantity</label>
                                <input name="quantity" type="number" class="form-control" id="productQuantity" value="{{ old('quantity', $isEdit ? $product->quantity : '') }}" placeholder="Enter quantity" min="0">
                                @error('quantity') <p class="text-danger">{{ $message }}</p> @enderror

                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label for="productStatus" class="form-label">Status</label>
                                <select name="status" class="form-select" id="productStatus">
                                    <option value="{{ ACTIVE_STATUS }}" {{ $isEdit && $product->status == ACTIVE_STATUS ? 'selected' : '' }}>Active</option>
                                    <option value="{{ INACTIVE_STATUS }}" {{ $isEdit && $product->status == INACTIVE_STATUS ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status') <p class="text-danger">{{ $message }}</p> @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="productDescription" class="form-label">Description</label>
                                <textarea name="description" class="form-control" id="productDescription" rows="4" placeholder="Enter product description">{{ old('description', $isEdit ? $product->description : '') }}</textarea>
                                @error('description') <p class="text-danger">{{ $message }}</p> @enderror
                            </div>

                            <!-- Comment -->
                            <div class="col-12">
                                <label for="productComment" class="form-label">Comment</label>
                                <textarea name="comment" class="form-control" id="productComment" rows="4" placeholder="Enter product comment"></textarea>
                            </div>

                            <!-- Submit / Action Buttons -->
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> {{ $isEdit ? 'Update' : 'Save' }} Product</button>

                                @if($isEdit)
                                    @if(!$product->trashed())
                                        <a id="deleteProduct" href="#" data-url="{{ route('product.delete-product', $product->id) }}" class="btn btn-danger">
                                            <i class="fa fa-trash"></i> Move to Trash
                                        </a>
                                    @endif

                                    @if($product->trashed())
                                        <a id="restoreProduct" href="#" data-url="{{ route('product.product-restore', $product->id) }}" class="btn btn-success">
                                            <i class="fa-solid fa-recycle"></i> Restore Product
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            const file = event.target.files[0];
            if(file){
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            } else if(!preview.src) {
                preview.classList.add('d-none');
            }
        }

        $(document).ready(function () {

            $('#productStatus').select2({
                minimumResultsForSearch: Infinity,
                width: '100%',
                dropdownParent: $('#productForm')
            });
        });

        $('#deleteProduct').on('click', function(e){
            e.preventDefault();
            const url = $(this).data('url');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to move this product to trash!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, move it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed){
                    window.location.href = url
                }
            });
        });

        $('#restoreProduct').on('click', function(e){
            e.preventDefault();
            const url = $(this).data('url');

            Swal.fire({
                title: 'Restore Product?',
                text: "This will restore the product from trash!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, restore it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed){
                    window.location.href = url
                }
            });
        });

        $('#productFormElement').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                sku: {
                    required: true,
                    minlength: 2
                },
                price: {
                    required: true,
                    number: true,
                    min: 0
                },
                quantity: {
                    required: true,
                    number: true,
                    min: 0
                },
                status: {
                    required: true
                },
                description: {
                    required: true,
                    minlength: 5
                },
                image: {
                    required: function() {
                        return {{ $isEdit ? 'false' : 'true' }};
                    },
                    extension: "jpg|jpeg|png"
                }
            },
            messages: {
                name: {
                    required: "Product name is required",
                    minlength: "Enter at least 3 characters"
                },
                sku: {
                    required: "SKU is required",
                    minlength: "Enter at least 2 characters"
                },
                price: {
                    required: "Price is required",
                    number: "Enter a valid number",
                    min: "Price cannot be negative"
                },
                quantity: {
                    required: "Quantity is required",
                    number: "Enter a valid number",
                    min: "Quantity cannot be negative"
                },
                status: {
                    required: "Please select status"
                },
                description: {
                    required: "Description is required",
                    minlength: "Enter at least 5 characters"
                },
                image: {
                    required: "Product image is required",
                    extension: "Allowed file types: jpg, jpeg, png, gif"
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });

    </script>
@endpush
