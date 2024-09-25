@extends('layouts.user_type.auth')

@section('content')

  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-lg-8">
        <div class="row">
          <div class="col-xl-6 mb-xl-0 mb-4">
            <div class="card bg-transparent shadow-xl">
              <div class="overflow-hidden position-relative border-radius-xl" style="background-image: url('../assets/img/curved-images/curved14.jpg');">
                <span class="mask bg-gradient-dark"></span>
                <div class="card-body position-relative z-index-1 p-3">
                  <i class="fas fa-wifi text-white p-2"></i>
                    <h5 class="text-white mt-4 mb-5 pb-2">{{  preg_replace('/(\d{4})(?=(\d{4})+$)/', '$1 ', auth()->user()->billingInfo()->first()->card_number) }}</h5>                  <div class="d-flex">
                    <div class="d-flex">
                      <div class="me-4">
                        <p class="text-white text-sm opacity-8 mb-0">Card Holder</p>
                        <h6 class="text-white mb-0">{{ auth()->user()->billingInfo()->first()->card_holder_name }}</h6>
                      </div>
                      <div>
                        <p class="text-white text-sm opacity-8 mb-0">Expires</p>
                        <h6 class="text-white mb-0">{{ Carbon::parse(auth()->user()->billingInfo()->first()->card_expiration_date)->format('m/y')}}</h6>
                      </div>
                    </div>
                    <div class="ms-auto w-20 d-flex align-items-end justify-content-end">
                      <img class="w-60 mt-2" src="../assets/img/logos/mastercard.png" alt="logo">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-6">
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header mx-4 p-3 text-center">
                    <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                      <i class="fas fa-landmark opacity-10"></i>
                    </div>
                  </div>
                  <div class="card-body pt-0 p-3 text-center">
                    <h6 class="text-center mb-0">Salary</h6>
                    <span class="text-xs">Belong Interactive</span>
                    <hr class="horizontal dark my-3">
                    <h5 class="mb-0">+$2000</h5>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mt-md-0 mt-4">
                <div class="card">
                  <div class="card-header mx-4 p-3 text-center">
                    <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                      <i class="fab fa-paypal opacity-10"></i>
                    </div>
                  </div>
                  <div class="card-body pt-0 p-3 text-center">
                    <h6 class="text-center mb-0">Paypal</h6>
                    <span class="text-xs">Freelance Payment</span>
                    <hr class="horizontal dark my-3">
                    <h5 class="mb-0">$455.00</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 mb-lg-0 mb-4">
            <div class="card mt-4">
              <div class="card-header pb-0 p-3">
                <div class="row">
                  <div class="col-6 d-flex align-items-center">
                    <h6 class="mb-0">Payment Method</h6>
                  </div>
                  <div class="col-6 text-end">
                    <a class="btn bg-gradient-dark mb-0" id="open-form-button" href="javascript:;"><i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Card</a>
                  </div>

                  <div id="overlay-form" style="display: none;">
                    <div class="container-fluid py-4">
                      <div class="card">
                          <div class="card-header pb-0 px-3">
                              <h6 class="mb-0">{{ __('Payment Method Information') }}</h6>
                          </div>
                          <div class="card-body pt-4 p-3">
                              <form action="/billing" method="POST" role="form text-left">
                                  @csrf
                                  @if($errors->any())
                                      <div class="mt-3  alert alert-primary alert-dismissible fade show" role="alert">
                                          <span class="alert-text text-white">
                                          {{$errors->first()}}</span>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                              <i class="fa fa-close" aria-hidden="true"></i>
                                          </button>
                                      </div>
                                  @endif
                                  @if(session('success'))
                                      <div class="m-3  alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                                          <span class="alert-text text-white">
                                          {{ session('success') }}</span>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                              <i class="fa fa-close" aria-hidden="true"></i>
                                          </button>
                                      </div>
                                  @endif
                                  <div class="row">
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="user-name" class="form-control-label">{{ __('Card holder name') }}</label>
                                              <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                                  <input class="form-control"  type="text" placeholder="Name" id="username" name="card_holder_name">
                                                      @error('name')
                                                          <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                      @enderror
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="user-email" class="form-control-label">{{ __('Card number') }}</label>
                                              <div class="@error('email')border border-danger rounded-3 @enderror">
                                                  <input class="form-control" type="text" placeholder="1234 5678 9112" id="user-email" name="card_number">
                                                      @error('email')
                                                          <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                      @enderror
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user-email" class="form-control-label">{{ __('Exp Date') }}</label>
                                            <div class="@error('email')border border-danger rounded-3 @enderror">
                                                <input class="form-control" type="date"  id="user-email" name="card_expiration_date">
                                                    @error('email')
                                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                    @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                          <label for="user-email" class="form-control-label">{{ __('Payment Method') }}</label>
                                          
                                            <select class="form-select" name="payment_method">
                                              <option value="credit_card">Thẻ tín dụng</option>
                                              <option value="e_wallet">Ví điện tử</option>
                                            </select>
                                      </div>
                                  </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="user.phone" class="form-control-label">{{ __('Card security code') }}</label>
                                              <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                                  <input class="form-control" type="tel" placeholder="123" id="number" name="card_security_code">
                                                      @error('phone')
                                                          <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                      @enderror
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="user.location" class="form-control-label">{{ __('Billing address') }}</label>
                                              <div class="@error('user.location') border border-danger rounded-3 @enderror">
                                                  <input class="form-control" type="text" placeholder="Address" id="name" name="billing_address">
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="d-flex justify-content-end">
                                      <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Save Changes' }}</button>
                                  </div>
                              </form>
              
                          </div>
                      </div>
                  </div>
                  </div>
                  <script>
                    document.getElementById("open-form-button").addEventListener("click", function() {
                    const overlayForm = document.getElementById("overlay-form");
                      if (overlayForm.style.display === "block") {
                        overlayForm.style.display = "none";
                      } else {
                        overlayForm.style.display = "block";
                        }
                          });
                    </script>

                </div>
              </div>
              <div class="card-body p-3">
                <div class="row">
                  {{-- <div class="col-md-6 mb-md-0 mb-4">
                    <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                      <img class="w-10 me-3 mb-0" src="../assets/img/logos/mastercard.png" alt="logo">
                      <h6 class="mb-0">****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;7852</h6>
                      <i class="fas fa-pencil-alt ms-auto text-dark cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Card"></i>
                    </div>
                  </div> --}}
                  @foreach ($BillingInfo as $item)
                  <div class="col-md-6">
                    <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                      <img class="w-10 me-3 mb-0" src="../assets/img/logos/visa.png" alt="logo">
                      <h6 class="mb-0">{{ '**** **** **** ' . substr($item->card_number, -4) }}</h6>
                      <i class="fas fa-pencil-alt ms-auto text-dark cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" 
                      data-billing-id = "{{ $item->billing_id }}"
                      data-card-number="{{ $item->card_number }}" data-cardholder-name="{{ $item->card_holder_name }}"
                      data-payment-method="{{ $item->payment_method }}" data-exp-date="{{ $item->expiration_date }}"
                      data-billing-address = "{{ $item->billing_address }}"onclick="loadForm(this)"></i>
                         
                        <i class="delete cursor-pointer fas fa-trash text-secondary" style="cursor: pointer" title="Delete" data-bs-toggle="tooltip" data-bs-placement="top" data-id={{ $item->billing_id }}></i>
                        
                        
                    </div>
                  </div>
                  @endforeach
                  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            $(document).on('click', '.delete', function () {
                                const Id = $(this).data('id');
                                const url = `/billing/${Id}`;
                        
                                if (confirm('Are you sure you want to delete?')) {
                                    $.ajax({
                                        type: 'DELETE',
                                        url: url,
                                        data: {
                                            _token: '{{ csrf_token() }}' // Include CSRF token
                                        },
                                        success: function (response) {
                                            if (response.success) {
                                                alert('Record deleted successfully!');
                                                location.reload(); // Refresh to see changes
                                            }
                                        },
                                        error: function (xhr) {
                                            alert('Error deleting record: ' + xhr.responseText);
                                        }
                                    });
                                }
                            });
                        </script>
                  <script>
                    function loadForm(element) {
                    const cardNumber = element.getAttribute('data-card-number');
                    const cardholderName = element.getAttribute('data-cardholder-name');
                    const billingId = element.getAttribute('data-billing-id');
                    const paymentMethod = element.getAttribute('data-payment-method');
                    const billingAddress = element.getAttribute('data-billing-address');
                    const overlayForm = document.getElementById("overlay-form-update");
                    document.getElementById('billing_id').value = billingId;
                    document.getElementById('card_number-update').value = cardNumber;
                    document.getElementById('card_holder_name-update').value = cardholderName;
                    document.getElementById('payment_method-update').value = paymentMethod;
                    document.getElementById('billing_address-update').value = billingAddress;
                    
                      
                        overlayForm.style.display = "block";
              
                          };
                    </script>
                  <div id="overlay-form-update" style="display: block;">
                    <div class="container-fluid py-4">
                      <div class="card">
                          <div class="card-header pb-0 px-3">
                              <h6 class="mb-0">{{ __('Payment Method Information') }}</h6>
                          </div>
                          <div class="card-body pt-4 p-3">
                              <form action="/billing" method="POST" role="form text-left">
                                  @csrf
                                  <input type="hidden" name="billing_id" id="billing_id" >
                                  @method('PATCH')
                                  @if($errors->any())
                                      <div class="mt-3  alert alert-primary alert-dismissible fade show" role="alert">
                                          <span class="alert-text text-white">
                                          {{$errors->first()}}</span>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                              <i class="fa fa-close" aria-hidden="true"></i>
                                          </button>
                                      </div>
                                  @endif
                                  @if(session('success'))
                                      <div class="m-3  alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                                          <span class="alert-text text-white">
                                          {{ session('success') }}</span>
                                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                              <i class="fa fa-close" aria-hidden="true"></i>
                                          </button>
                                      </div>
                                  @endif
                                  <div class="row">
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="user-name" class="form-control-label">{{ __('Card holder name') }}</label>
                                              <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                                  <input class="form-control"  type="text" placeholder="Name" id="card_holder_name-update" name="card_holder_name">
                                                      @error('name')
                                                          <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                      @enderror
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="user-email" class="form-control-label">{{ __('Card number') }}</label>
                                              <div class="@error('email')border border-danger rounded-3 @enderror">
                                                  <input class="form-control" type="text" placeholder="1234 5678 9112" id="card_number-update" name="card_number">
                                                      @error('email')
                                                          <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                      @enderror
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user-email" class="form-control-label">{{ __('Exp Date') }}</label>
                                            <div class="@error('email')border border-danger rounded-3 @enderror">
                                                <input class="form-control" type="date"  id="card_expiration_date-update" name="card_expiration_date">
                                                    @error('email')
                                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                    @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                          <label for="user-email" class="form-control-label">{{ __('Payment Method') }}</label>
                                          
                                            <select class="form-select" name="payment_method" id="payment_method-update">
                                              <option value="credit_card">Thẻ tín dụng</option>
                                              <option value="e_wallet">Ví điện tử</option>
                                            </select>
                                      </div>
                                  </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="user.phone" class="form-control-label">{{ __('Card security code') }}</label>
                                              <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                                  <input class="form-control" type="tel" placeholder="123" id="number" name="card_security_code">
                                                      @error('phone')
                                                          <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                      @enderror
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="user.location" class="form-control-label">{{ __('Billing address') }}</label>
                                              <div class="@error('user.location') border border-danger rounded-3 @enderror">
                                                  <input class="form-control" type="text" placeholder="Address" id="billing_address-update" name="billing_address">
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  
                                      <button type="submit" class="btn bg-gradient-dark btn-md mt-4 mb-4">{{ 'Save Changes' }}</button>
                                  </div>
                              </form>
              
                          </div>
                      </div>
                  </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-header pb-0 p-3">
            <div class="row">
              <div class="col-6 d-flex align-items-center">
                <h6 class="mb-0">Invoices</h6>
              </div>
              <div class="col-6 text-end">
                <button class="btn btn-outline-primary btn-sm mb-0">View All</button>
              </div>
            </div>
          </div>
          <div class="card-body p-3 pb-0">
            <ul class="list-group">
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex flex-column">
                  <h6 class="mb-1 text-dark font-weight-bold text-sm">March, 01, 2020</h6>
                  <span class="text-xs">#MS-415646</span>
                </div>
                <div class="d-flex align-items-center text-sm">
                  $180
                  <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                </div>
              </li>
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex flex-column">
                  <h6 class="text-dark mb-1 font-weight-bold text-sm">February, 10, 2021</h6>
                  <span class="text-xs">#RV-126749</span>
                </div>
                <div class="d-flex align-items-center text-sm">
                  $250
                  <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                </div>
              </li>
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex flex-column">
                  <h6 class="text-dark mb-1 font-weight-bold text-sm">April, 05, 2020</h6>
                  <span class="text-xs">#FB-212562</span>
                </div>
                <div class="d-flex align-items-center text-sm">
                  $560
                  <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                </div>
              </li>
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex flex-column">
                  <h6 class="text-dark mb-1 font-weight-bold text-sm">June, 25, 2019</h6>
                  <span class="text-xs">#QW-103578</span>
                </div>
                <div class="d-flex align-items-center text-sm">
                  $120
                  <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                </div>
              </li>
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                <div class="d-flex flex-column">
                  <h6 class="text-dark mb-1 font-weight-bold text-sm">March, 01, 2019</h6>
                  <span class="text-xs">#AR-803481</span>
                </div>
                <div class="d-flex align-items-center text-sm">
                  $300
                  <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i class="fas fa-file-pdf text-lg me-1"></i> PDF</button>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-7 mt-4">
        <div class="card">
          <div class="card-header pb-0 px-3">
            <h6 class="mb-0">Billing Information</h6>
          </div>
          <div class="card-body pt-4 p-3">
            <ul class="list-group">
              <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                <div class="d-flex flex-column">
                  <h6 class="mb-3 text-sm">Oliver Liam</h6>
                  <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Viking Burrito</span></span>
                  <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">oliver@burrito.com</span></span>
                  <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                </div>
                <div class="ms-auto text-end">
                  <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                  <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                </div>
              </li>
              <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                <div class="d-flex flex-column">
                  <h6 class="mb-3 text-sm">Lucas Harper</h6>
                  <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Stone Tech Zone</span></span>
                  <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">lucas@stone-tech.com</span></span>
                  <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                </div>
                <div class="ms-auto text-end">
                  <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                  <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                </div>
              </li>
              <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                <div class="d-flex flex-column">
                  <h6 class="mb-3 text-sm">Ethan James</h6>
                  <span class="mb-2 text-xs">Company Name: <span class="text-dark font-weight-bold ms-sm-2">Fiber Notion</span></span>
                  <span class="mb-2 text-xs">Email Address: <span class="text-dark ms-sm-2 font-weight-bold">ethan@fiber.com</span></span>
                  <span class="text-xs">VAT Number: <span class="text-dark ms-sm-2 font-weight-bold">FRB1235476</span></span>
                </div>
                <div class="ms-auto text-end">
                  <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="javascript:;"><i class="far fa-trash-alt me-2"></i>Delete</a>
                  <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;"><i class="fas fa-pencil-alt text-dark me-2" aria-hidden="true"></i>Edit</a>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-5 mt-4">
        <div class="card h-100 mb-4">
          <div class="card-header pb-0 px-3">
            <div class="row">
              <div class="col-md-6">
                <h6 class="mb-0">Your Transaction's</h6>
              </div>
              <div class="col-md-6 d-flex justify-content-end align-items-center">
                <i class="far fa-calendar-alt me-2"></i>
                <small>23 - 30 March 2020</small>
              </div>
            </div>
          </div>
          <div class="card-body pt-4 p-3">
            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Newest</h6>
            <ul class="list-group">
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex align-items-center">
                  <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-down"></i></button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">Netflix</h6>
                    <span class="text-xs">27 March 2020, at 12:30 PM</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold">
                  - $ 2,500
                </div>
              </li>
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex align-items-center">
                  <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">Apple</h6>
                    <span class="text-xs">27 March 2020, at 04:30 AM</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                  + $ 2,000
                </div>
              </li>
            </ul>
            <h6 class="text-uppercase text-body text-xs font-weight-bolder my-3">Yesterday</h6>
            <ul class="list-group">
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex align-items-center">
                  <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">Stripe</h6>
                    <span class="text-xs">26 March 2020, at 13:45 PM</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                  + $ 750
                </div>
              </li>
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex align-items-center">
                  <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">HubSpot</h6>
                    <span class="text-xs">26 March 2020, at 12:30 PM</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                  + $ 1,000
                </div>
              </li>
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex align-items-center">
                  <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">Creative Tim</h6>
                    <span class="text-xs">26 March 2020, at 08:30 AM</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                  + $ 2,500
                </div>
              </li>
              <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                <div class="d-flex align-items-center">
                  <button class="btn btn-icon-only btn-rounded btn-outline-dark mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="fas fa-exclamation"></i></button>
                  <div class="d-flex flex-column">
                    <h6 class="mb-1 text-dark text-sm">Webflow</h6>
                    <span class="text-xs">26 March 2020, at 05:00 AM</span>
                  </div>
                </div>
                <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                  Pending
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
 
@endsection

