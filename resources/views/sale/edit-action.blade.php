@extends('layout.master')
@section('title',$title)
@push('style')
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Update New Sale Record</h5>
                </div>
                <div class="card-body" id="saleForm">
                    <form id="orderForm">
                        <input type="hidden" name="saleId" value="{{$sale->id}}">
                        <!-- Customer Select -->
                        <div class="mb-3">
                            <label class="form-label">Select Customer</label>
                            <select class="form-select" id="customer_id" name="customer_id">
                                @if($sale->customer)
                                    <option value="{{$sale->customer->id}}">{{$sale->customer->name}}</option>
                                @else
                                    <option value="">-- Select Customer --</option>
                                @endif

                            </select>
                        </div>

                        <!-- Product Rows -->
                        <div id="productRows">
                            @foreach($sale->items as $key => $item)
                                <div class="row g-2 align-items-end product-row mb-2">
                                    <input type="hidden" class="form-control" name="products[{{$key}}][salesItemId]" value="{{$item->id }}">
                                    <div class="col-md-3">
                                        <label class="form-label">Product</label>
                                        <select class="form-select product-select" name="products[{{$key}}][id]"
                                                style="width:100%;">
                                            @if($item->product)
                                                <option value="{{$item->product->id}}"
                                                        selected>{{$item->product->name}}</option>
                                            @else
                                                <option value="">-- Select Product --</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Qty</label>
                                        <input type="number" min="1" class="form-control qty"
                                               name="products[{{$key}}][qty]" value="{{$item->quantity ?? 1}}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Price</label>
                                        <input type="number" step="0.01" class="form-control price"
                                               value="{{$item->price ?? 1}}" name="products[{{$key}}][price]" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Discount</label>
                                        <input type="number" step="0.01" class="form-control discount"
                                               value="{{$item->discount ?? 0}}" name="products[{{$key}}][discount]">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Total</label>
                                        <input type="number" step="0.01" class="form-control total"
                                               value="{{$item->total ?? ''}}" name="products[{{$key}}][total]" readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger removeRow">X</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Add More Button -->
                        <button type="button" class="btn btn-secondary mb-3" id="addRow">+ Add More</button>

                        <!-- Grand Total -->
                        <div class="mb-3">
                            <label class="form-label">Grand Total</label>
                            <input type="number" step="0.01" class="form-control" id="grandTotal" name="grandTotal" value="{{$sale->total_amount ?? ''}}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sale Comment</label>
                            @if(!empty($sale->note))
                                @foreach($sale->note as $note)
                                    <textarea name="comment" class="form-control" id="saleComment" rows="4" placeholder="Enter sale comment">{{$note->note}}</textarea>
                                @endforeach
                            @else
                                <textarea name="comment" class="form-control" id="saleComment" rows="4" placeholder="Enter sale comment"></textarea>
                            @endif

                        </div>

                        <!-- Submit -->
                        <button type="submit" id="submitBtn" class="btn btn-primary">
                            <span class="btn-text">Update</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>

                    <!-- Feedback -->
                    <div id="feedback" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#customer_id").select2({
                placeholder: "-- Select Customer --",
                allowClear: true,
                dropdownParent: $('#saleForm'),
                ajax: {
                    url: `{{route('sale.search-customers')}}`,
                    dataType: "json",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {id: item.id, text: item.name};
                            })
                        };
                    },
                    cache: true
                }
            });
        });

        function initProductSelect2(element) {
            $(element).select2({
                placeholder: "-- Select Product --",
                allowClear: true,
                ajax: {
                    url: `{{route('sale.search-products')}}`,
                    dataType: "json",
                    delay: 250,
                    data: function (params) {
                        return {q: params.term};
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    price: item.price,
                                    quantity: item.quantity
                                };
                            })
                        };
                    },
                    cache: true
                }
            }).on("select2:select", function (e) {
                    let data = e.params.data;
                    let row = $(this).closest(".product-row");

                    row.find(".price").val(data.price);
                    row.find(".qty").val(1); // default 1
                    row.find(".qty").attr("max", data.quantity); // optional: set max available
                    row.find(".discount").val(0);

                    // calculate total for that row
                    let total = (1 * data.price) - 0;
                    row.find(".total").val(total.toFixed(2));

                    calculateGrandTotal();
                });
        }

        initProductSelect2(".product-select");

        let rowIndex = `{{count($sale->items)}}`;

        $("#addRow").click(function () {
            let newRow = `
              <div class="row g-2 align-items-end product-row mb-2">
                <div class="col-md-3">
                  <label class="form-label">Product</label>
                  <select class="form-select product-select" name="products[${rowIndex}][id]" style="width:100%;">
                    <option value="">-- Select Product --</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Qty</label>
                  <input type="number" min="1" class="form-control qty" name="products[${rowIndex}][qty]" value="1">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Price</label>
                  <input type="number" step="0.01" class="form-control price" name="products[${rowIndex}][price]" readonly>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Discount</label>
                  <input type="number" step="0.01" class="form-control discount" name="products[${rowIndex}][discount]" value="0">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Total</label>
                  <input type="number" step="0.01" class="form-control total" name="products[${rowIndex}][total]" readonly>
                </div>
                <div class="col-md-1">
                  <button type="button" class="btn btn-danger removeRow">X</button>
                </div>
              </div>
            `;

            $("#productRows").append(newRow);
            initProductSelect2(`#productRows .product-row:last .product-select`);
            rowIndex++;
        });

        $(document).on("click", ".removeRow", function () {
            if ($(".product-row").length > 1) {
                $(this).closest(".product-row").remove();
                calculateGrandTotal();
            }
        });

        $(document).on("input", ".qty, .discount", function () {
            let row = $(this).closest(".product-row");
            calculateRowTotal(row)
            calculateGrandTotal();
        });

        function calculateRowTotal(row) {
            let qty = parseFloat(row.find(".qty").val()) || 0;
            let price = parseFloat(row.find(".price").val()) || 0;
            let discount = parseFloat(row.find(".discount").val()) || 0;

            let subtotal = qty * price;
            let discountAmount = subtotal * (discount / 100);
            let total = subtotal - discountAmount;

            row.find(".total").val(total.toFixed(2));
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let grand = 0;
            $(".total").each(function () {
                grand += parseFloat($(this).val()) || 0;
            });
            $("#grandTotal").val(grand.toFixed(2));
        }

        document.getElementById("orderForm").addEventListener("submit", function (e) {
            e.preventDefault();

            let isValid = true;
            let errors = [];

            const submitBtn = document.getElementById("submitBtn");
            const spinner = submitBtn.querySelector(".spinner-border");
            const btnText = submitBtn.querySelector(".btn-text");


            submitBtn.disabled = true;
            spinner.classList.remove("d-none");
            btnText.textContent = "Submitting...";

            // Customer validation
            let customer = document.getElementById("customer_id").value;
            if (!customer) {
                isValid = false;
                errors.push("Customer is required.");
                console.log('customer')
            }

            // Product validation
            let productRows = document.querySelectorAll(".product-row");
            if (productRows.length === 0) {
                isValid = false;
                errors.push("At least one product is required.");
                console.log('At least')


            } else {
                productRows.forEach((row, index) => {
                    let product = row.querySelector(".product-select").value;
                    let price = row.querySelector(".price").value;
                    let total = row.querySelector(".total").value;

                    if (!product) {
                        isValid = false;
                        errors.push(`Product is required in row ${index + 1}.`);
                        console.log('Product')


                    }

                    if (!price || price <= 0) {
                        isValid = false;
                        errors.push(`Price is required in row ${index + 1}.`);
                        console.log('Price')

                    }
                    if (!total || total <= 0) {
                        isValid = false;
                        errors.push(`Total must be greater than 0 in row ${index + 1}.`);
                        console.log('Total')

                    }
                });
            }

            // Grand total validation
            let grandTotal = document.getElementById("grandTotal").value;
            if (!grandTotal || grandTotal <= 0) {
                isValid = false;
                errors.push("Grand total must be greater than 0.");
                console.log('Grand')
            }

            // Show errors or submit
            let feedback = document.getElementById("feedback");
            feedback.innerHTML = "";

            if (!isValid) {
                feedback.innerHTML = `<div class="alert alert-danger"><ul>${errors.map(e => `<li>${e}</li>`).join("")}</ul></div>`;
                return;
            }

            const formData = new FormData(this);
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('sale.update-sale') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': token
                },
                body: formData
            })
                .then(async res => {
                    let responseData;
                    try {
                        responseData = await res.json();
                    } catch {
                        throw new Error("Server returned invalid JSON");
                    }

                    if (!res.ok) {
                        let errorMsg = responseData?.message || "Unexpected error";

                        if (responseData?.errors) {
                            errorMsg = Object.values(responseData.errors).flat().join("\n");
                        }

                        throw new Error(errorMsg);
                    }

                    return responseData;
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        text: data.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    document.getElementById("feedback").innerHTML =
                        `<div class="alert alert-success">${data.message}</div>`;
                    setTimeout(function (){
                        window.location.reload();
                    },2000)
                    document.getElementById("grandTotal").value = "";
                })
                .catch(err => {
                    document.getElementById("feedback").innerHTML =
                        `<div class="alert alert-danger">Error saving order: ${err.message}</div>`;
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    spinner.classList.add("d-none");
                    btnText.textContent = "Submit";
                });
        });


    </script>
@endpush
