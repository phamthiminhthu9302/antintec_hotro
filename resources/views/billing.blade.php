@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-12">
      <div class="row">
        <div class="col-xl-6 mb-xl-0 mb-4">
          <div class="card bg-transparent shadow-xl">
            <div class="overflow-hidden position-relative border-radius-xl" style="background-image: url('../assets/img/curved-images/curved14.jpg');">
              <span class="mask bg-gradient-dark"></span>
              <div class="card-body position-relative z-index-1 p-3">
                <i class="fas fa-wifi text-white p-2"></i>
                <h5 class="text-white mt-4 mb-5 pb-2">{{ auth()->user()->billingInfo()->first()?  '**** **** **** ' . substr(auth()->user()->billingInfo()->first()->card_number, -4):"No card information"}}</h5>
                <div class="d-flex">
                  <div class="d-flex">
                    <div class="me-4">
                      <p class="text-white text-sm opacity-8 mb-0">Card Holder</p>
                      <h6 class="text-white mb-0">{{auth()->user()->billingInfo()->first()? auth()->user()->billingInfo()->first()->card_holder_name:" " }}</h6>
                    </div>
                    <div>
                      <p class="text-white text-sm opacity-8 mb-0">Expires</p>
                      <h6 class="text-white mb-0">{{ auth()->user()->billingInfo()->first()? Carbon::parse(auth()->user()->billingInfo()->first()->card_expiration_date)->format('m/y'): ""}}</h6>
                    </div>
                  </div>
                  <div class="ms-auto w-20 d-flex align-items-end justify-content-end">
                    <img class="w-60 mt-2" src="../assets/img/logos/credit_card.png" alt="logo">
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
                  <h6 class="text-center mb-0">Deposit</h6>
                  <span class="text-xs">{{$totalAmount}}</span>
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
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                  <span class="alert-text text-white">
                    {{ session('success') }}</span>
                  <div type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                  </div>
                </div>
                @endif
                <div id="overlay-form" style="display: none;">
                  <div class="container-fluid py-4">
                    <div class="card">
                      <div class="card-header pb-0 px-3">
                        <h6 class="mb-0">{{ __('Payment Method Information') }}</h6>
                      </div>
                      <div class="card-body pt-4 p-3">
                        <form action="/billing" method="POST" role="form text-left">
                          @csrf
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="user-name" class="form-control-label">{{ __('Card holder name') }}</label>
                                <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                  <input class="form-control" type="text" placeholder="Name" id="username" name="card_holder_name" value={{ old('card_holder_name') }}>
                                  @error('card_holder_name')
                                  <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                  @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="user-email" class="form-control-label">{{ __('Card number') }}</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                  <input class="form-control" type="text" placeholder="1234 5678 9112" id="user-email" name="card_number" value={{ old('card_number')}}>
                                  @error('card_number')
                                  <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                  @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="user-email" class="form-control-label">{{ __('Exp Date') }}</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                  <input class="form-control" type="date" id="user-email" name="card_expiration_date" value={{ old('card_expiration_date')}}>
                                  @error('card_expiration_dat')
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
                                  <input class="form-control" type="tel" placeholder="123" id="number" name="card_security_code" value={{ old('card_security_code')}}>
                                  @error('card_security_code')
                                  <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                  @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="user.location" class="form-control-label">{{ __('Billing address') }}</label>
                                <div class="@error('user.location') border border-danger rounded-3 @enderror">
                                  <input class="form-control" type="text" placeholder="Address" id="name" name="billing_address" value={{ old('billing_address')}}>
                                  @error('billing_address')
                                  <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                  @enderror
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
                  <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row" style="margin-bottom:20px;">
                    <img class="w-10 me-3 mb-0" src="../assets/img/logos/{{ $item->payment_method }}.png" alt="logo">
                    <h6 class="mb-0">{{ '**** **** **** ' . substr($item->card_number, -4) }}</h6>
                    <i class="fas fa-pencil-alt ms-auto text-dark cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                      data-billing-id="{{ $item->billing_id }}"
                      data-card-number="{{ $item->card_number }}" data-cardholder-name="{{ $item->card_holder_name }}"
                      data-payment-method="{{ $item->payment_method }}" data-exp-date="{{ $item->expiration_date }}" data-card-security-code="{{ $item->card_security_code }}"
                      data-billing-address="{{ $item->billing_address }}" onclick="loadForm(this)"></i>
                    <i class="delete cursor-pointer fas fa-trash text-secondary" style="cursor: pointer" title="Delete" data-bs-toggle="tooltip" data-bs-placement="top" data-id={{ $item->billing_id }}></i>
                  </div>
                </div>
                @endforeach
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                  $(document).on('click', '.delete', function() {
                    const Id = $(this).data('id');
                    const url = `/billing/${Id}`;

                    if (confirm('Are you sure you want to delete?')) {
                      $.ajax({
                        type: 'DELETE',
                        url: url,
                        data: {
                          _token: '{{ csrf_token() }}' // Include CSRF token
                        },
                        success: function(response) {
                          if (response.success) {
                            alert('Record deleted successfully!');
                            location.reload(); // Refresh to see changes
                          }
                        },
                        error: function(xhr) {
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
                    const card_security_code = element.getAttribute('data-card-security-code');
                    const billingAddress = element.getAttribute('data-billing-address');
                    const overlayForm = document.getElementById("overlay-form-update");
                    const exp_date = element.getAttribute('data-exp-date');

                    document.getElementById('billing_id').value = billingId;
                    document.getElementById('card_number-update').value = cardNumber;
                    document.getElementById('card_holder_name-update').value = cardholderName;
                    document.getElementById('payment_method-update').value = paymentMethod;
                    document.getElementById('billing_address-update').value = billingAddress;
                    document.getElementById('card_security_code-update').value = card_security_code;
                    document.getElementById('card_expiration_date-update').value = exp_date;
                    overlayForm.style.display = "block";
                  };
                </script>
                <div id="overlay-form-update" style="display: none;">
                  <div class="container-fluid py-4">
                    <div class="card">
                      <div class="card-header pb-0 px-3">
                        <h6 class="mb-0">{{ __('Payment Method Information Update') }}</h6>
                      </div>
                      <div class="card-body pt-4 p-3">
                        <form action="/billing-update" method="POST" role="form text-left">
                          @csrf
                          <input type="hidden" name="billing_id" id="billing_id">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="user-name" class="form-control-label">{{ __('Card holder name') }}</label>
                                <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                  <input class="form-control" type="text" placeholder="Name" id="card_holder_name-update" name="card_holder_name_update">
                                  @error('card_holder_name_update')
                                  <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                  @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="user-email" class="form-control-label">{{ __('Card number') }}</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                  <input class="form-control" type="text" placeholder="1234 5678 9112" id="card_number-update" name="card_number_update">
                                  @error('card_number_update')
                                  <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                  @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="user-email" class="form-control-label">{{ __('Exp Date') }}</label>
                                <div class="@error('email')border border-danger rounded-3 @enderror">
                                  <input class="form-control" type="date" id="card_expiration_date-update" name="card_expiration_date_update">
                                  @error('card_expiration_date_update')
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
                                  <input class="form-control" type="tel" placeholder="123" id="card_security_code-update" name="card_security_code_update">
                                  @error('card_security_code_update')
                                  <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                  @enderror
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="user.location" class="form-control-label">{{ __('Billing address') }}</label>
                                <div class="@error('user.location') border border-danger rounded-3 @enderror">
                                  <input class="form-control" type="text" placeholder="Address" id="billing_address-update" name="billing_address_update">
                                  @error('billing_address_update')
                                  <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                  @enderror
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
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 mb-lg-0 mb-4">
          <div class="card mt-4">
            <div class="card-header pb-0 p-3">
              <div class="row">
                <div class="col-6 d-flex align-items-center">
                  <h6 class="mb-0">Deposit</h6>
                </div>
                <div class="col-6 text-end">
                  <a class="btn bg-gradient-dark mb-0" id="open-form-deposit-button" href="javascript:;"><i class="fas fa-plus"></i>&nbsp;&nbsp;Add New Deposit</a>
                </div>
                <div class="card-body p-3"></div>
                @if(session('success_deposit'))
                <div class="alert alert-success alert-dismissible fade show" id="alert-success" role="alert">
                  <span class="alert-text text-white">
                    {{ session('success_deposit') }}</span>
                  <div type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fa fa-close" aria-hidden="true"></i>
                  </div>
                </div>
                @endif
                <div id="overlay-form-deposit" style="display: none;">
                  <div class="container-fluid py-4">
                    <div class="card">
                      <div style="display:flex;">
                        <button class="btn bg-gradient-dark " onclick="create_deposit_new()" style="width:300px; margin-right:70px;">Create deposit with payment new</button>
                        <button class="btn bg-gradient-dark " onclick="create_deposit_available()" style="width:300px;">Create deposit with payment available</button>
                      </div>
                      <script>
                        function create_deposit_new() {
                          var form = document.getElementById('deposit_new');
                          form.style.display = 'block';
                          var f = document.getElementById('deposit_available');
                          f.style.display = 'none';
                        }

                        function create_deposit_available() {
                          var form = document.getElementById('deposit_available');
                          form.style.display = 'block';
                          var f = document.getElementById('deposit_new');
                          f.style.display = 'none';
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                          var selectBox = document.getElementById('payment-method-select');
                          var selectedOption = selectBox.querySelector('.selected-option');
                          var options = selectBox.querySelector('.options');
                          var inputHidden = document.getElementById('payment-method-input');
                          var deposit_id = document.getElementById('deposit_id');
                          selectedOption.addEventListener('click', function() {
                            options.style.display = options.style.display === 'block' ? 'none' : 'block';
                          });
                          selectBox.querySelectorAll('.option').forEach(function(option) {
                            option.addEventListener('click', function() {
                              selectedOption.querySelector('span').textContent = option.querySelector('span').textContent;
                              deposit_id.value = option.getAttribute('data-value');
                              inputHidden.value = option.getAttribute('data-value');
                              options.style.display = 'none';
                            });
                          });
                          document.addEventListener('click', function(e) {
                            if (!selectBox.contains(e.target)) {
                              options.style.display = 'none';
                            }
                          });
                        });
                      </script>
                      <div id="deposit_available" style="display: none;">
                        <div class="card-header pb-0 px-3">
                          <h6 class="mb-0">{{ __('Deposit Information') }}</h6>
                        </div>
                        <div class="card-body pt-4 p-3">
                          <form action="/deposit-update" method="POST" role="form text-left">
                            @csrf
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                  <div class="@error('user.amount')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="Amount" id="user-amount-de" name="amount_available" value={{ old('amount') }}>
                                    @error('amount_available')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label for="user-email" class="form-control-label">{{ __('Payment Available ') }}</label>
                                  <div class="custom-select" id="payment-method-select">
                                    <div class="selected-option">
                                      <span style="margin-right:370px;">Select payment available</span>
                                      <i class="fa fa-chevron-down dropdown-icon"></i>
                                    </div>
                                    <div class="options">
                                      @foreach ($BillingInfo as $item)
                                      <div class="option" data-value="{{ $item->billing_id }}">
                                        <img class="w-10 me-3 mb-0" src="../assets/img/logos/{{ $item->payment_method }}.png" alt="logo">
                                        <span>{{ '**** **** **** ' . substr($item->card_number, -4) }}</span>
                                      </div>
                                      @endforeach
                                    </div>
                                    @error('payment_method_available')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                    <input type="hidden" name="payment_method_available" id="payment-method-input" />
                                    <input type="hidden" name="deposit_id" id="deposit_id" />
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
                      <div id="deposit_new" style="display: none;">
                        <div class="card-header pb-0 px-3">
                          <h6 class="mb-0">{{ __('Deposit Information') }}</h6>
                        </div>
                        <div class="card-body pt-4 p-3">
                          <form action="/deposit" method="POST" role="form text-left">
                            @csrf
                            <div class="row">
                              @if(session('error_exist'))
                              <p class="text-danger text-xs mt-2" id="error_exist">Payment information already exists.</p>
                              @endif
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                  <div class="@error('user.amount')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="Amount" id="user-amount-de" name="amount_new" value="{{ old('amount_new') }}">
                                    @error('amount_new')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label for="user-name" class="form-control-label">{{ __('Card holder name') }}</label>
                                  <div class="@error('user.name')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="Name" id="user-card-holder-name-de" name="card_holder_name_new" value={{ old('card_holder_name_new') }}>
                                    @error('card_holder_name_new')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label for="user-email" class="form-control-label">{{ __('Card number') }}</label>
                                  <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="1234 5678 9112" id="user-card-number-de" name="card_number_new" value={{ old('card_number_new')}}>
                                    @error('card_number_new')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label for="user-email" class="form-control-label">{{ __('Exp Date') }}</label>
                                  <div class="@error('email')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="date" id="user-exp-date-de" name="card_expiration_date_new" value={{ old('card_expiration_date_new')}}>
                                    @error('card_expiration_date_new')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label for="user-email" class="form-control-label">{{ __('Payment Method') }}</label>
                                  <select class="form-select" name="payment_method">
                                    <option value="credit_card">Thẻ tín dụng</option>
                                    <option value="e_wallet">Ví điện tử</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label for="user.phone" class="form-control-label">{{ __('Card security code') }}</label>
                                  <div class="@error('user.phone')border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="tel" placeholder="123" id="number-de" name="card_security_code_new" value={{ old('card_security_code_new') }}>
                                    @error('card_security_code_new')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label for="user.location" class="form-control-label">{{ __('Billing address') }}</label>
                                  <div class="@error('user.location') border border-danger rounded-3 @enderror">
                                    <input class="form-control" type="text" placeholder="Address" id="billing-address-de" name="billing_address_new" value={{ old('billing_address_new')}}>
                                    @error('billing_address_new')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
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
                </div>
                <script>
                  document.getElementById("open-form-deposit-button").addEventListener("click", function() {
                    const overlayForm = document.getElementById("overlay-form-deposit");
                    if (overlayForm.style.display === "block") {
                      overlayForm.style.display = "none";
                    } else {
                      overlayForm.style.display = "block";
                    }
                  });
                </script>
              </div>
            </div>
          </div>
        </div>
        <input type="hidden" id="has-error-new" value="{{$errors->has('amount_new') || $errors->has('card_holder_name_new') || $errors->has('card_number_new') || $errors->has('card_security_code_new') || $errors->has('billing_address_new') ? 'true' : 'false' }}">
        <input type="hidden" id="has-error-available" value="{{ $errors->has('amount_available') || $errors->has('payment_method_available') ? 'true' : 'false' }}">
        <input type="hidden" id="has-error" value="{{$errors->has('card_holder_name') || $errors->has('card_number') || $errors->has('card_security_code') || $errors->has('billing_address') ? 'true' : 'false' }}">
        <input type="hidden" id="has-error-update" value="{{  $errors->has('card_holder_name_update') || $errors->has('card_number_update') || $errors->has('card_security_code_update') || $errors->has('billing_address_update') ? 'true' : 'false' }}">
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            var hasError = document.getElementById('has-error').value;
            var hasErrorUpdate = document.getElementById('has-error-update').value;
            var errorExist = "{{ session('error_exist') }}";
            var billing_button = document.getElementById("overlay-form");
            var billing_button_update = document.getElementById("overlay-form-update");
            var hasErrorNew = document.getElementById('has-error-new').value;
            var hasErrorAvailable = document.getElementById('has-error-available').value;
            var deposit_button = document.getElementById("overlay-form-deposit");
            if (hasErrorNew === 'true' || errorExist === 'true') {
              document.getElementById('deposit_new').style.display = 'block'; // Mở form "deposit_new"
              deposit_button.style.display = 'block';
            }
            if (hasErrorAvailable === 'true') {
              document.getElementById('deposit_available').style.display = 'block'; // Mở form "deposit_available"
              deposit_button.style.display = 'block';
            }
            if (hasError === 'true') {
              billing_button.style.display = 'block';
            }
            if (hasErrorUpdate === 'true') {
              billing_button_update.style.display = 'block';
            }

          });
        </script>
      </div>
    </div>
  </div>
</div>
<style>
  .custom-select {
    position: relative;
    width: 100%;
  }

  .selected-option {
    border: 1px solid #d2d6da;
    border-radius: 0.3rem;
    color: #495057;
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 1.4rem;
    padding: 0.5rem 0.75rem;
    transition: box-shadow 0.15s ease, border-color 0.15s ease;
    margin-top: 4px;
  }

  .options {
    display: none;
    background: #fff;
    border: 1px solid #d2d6da;
    max-height: 200px;
    overflow-y: auto;
    position: absolute;
    width: 100%;
    z-index: 999;
  }

  .option {
    display: flex;
    align-items: center;
    padding: 10px;
    cursor: pointer;
  }

  .option img {
    width: 40px;
    height: auto;
    margin-right: 10px;
  }

  .option:hover {
    background-color: #f1f1f1;
  }
</style>
@endsection