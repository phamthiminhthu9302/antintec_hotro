@extends('layouts.user_type.auth')

@section('content')
<button onclick="test(1,'completed')">Test Thông báo</button>
<div class="google-map-customer">
  <div class="sidebar-form-main">
    @if(Auth::check())
    @if(Auth::user()->role === 'customer')
    <nav class="nav-side-bar-main">
      <a href="#first"><img width="36px" id="iconService" height="36px" src="../assets/img/location.png" alt="ic_service"></a>
    </nav>
    @endif
    @endif

    <div class="container-double">
      <section id="first" class="section-form-service">
        <div class="form-container" id="form-service">
          <div id="input-container">
            <div class="header-form-service">
              <h3 class="form-title"> Tìm kiếm dịch vụ</h3>
              <img width="16px" height="16px" id="toggleServiceIcon" class="icon-arrow-left" src="../assets/img/left-arrow.png" alt="ic_arrow_left">
            </div>
            <form id="service-form" method="POST" action="/filterServices">
              <div class="form-group">
                <input type="hidden" name="role" id="role" value="{{ auth()->user()->role }}">
                <input type="hidden" name="userId" id="userId" value="{{ auth()->user()->user_id }}">
              </div>
              <div class="form-group">
                <select class="form-select" id="service_type" name="service_id" aria-label="Default select example">
                  <option selected>Chọn dịch vụ</option>
                  @foreach($services as $service)
                  <option value="{{ $service->service_id }}" data-service-id="{{ $service->service_id }}" data-name="{{ $service->name }}" data-price="{{ $service->price }}">Dịch vụ: {{ $service->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <!-- <select class="form-select" id="service-form-price" name="service_id" aria-label="Default select example">
                  <option selected>Chọn giá</option>
                  @foreach($services as $service)
                  <option value="{{ $service->service_id }}">Giá: {{ number_format($service->price, 0, ',', '.') }}</option>
                  @endforeach
                </select> -->
                <label for="service_price">Giá:</label>
                <select class="form-select" id="service-form-price" name="price" aria-label="Default select example" disabled>
                  <option selected>Chọn giá</option>
                  <!-- Giá sẽ được cập nhật ở đây -->
                </select>
              </div>
              <div class="form-group">
                <button type="submit" id="btn-search-service">Tìm kiếm</button>
                <button id="btn-book-now">Đặt ngay</button>
              </div>
              <hr class="hr-color">
              <ul id="service-list" class="error-service" style="display: block;"></ul>
            </form>
          </div>
        </div>
      </section>
    </div>
    <!-- Popup form yêu cầu -->
    <div id="request-popup" class="custom-popup" style="display: none;">
      <div class="custom-popup-content">
        <h3 class="title-rescue">Gửi yêu cầu cứu hộ</h2>
          <form id="request-form" method="POST" action="/save-request">
            @csrf
            <div class="form-row">
              <input type="hidden" name="role" id="role" value="{{ auth()->user()->role }}">
              <input type="hidden" name="customer_id" id="userId" value="{{ auth()->user()->user_id }}">
              <input type="hidden" name="status" id="status" value="pending">
              <input type="hidden" name="requested_at" id="requested_at">
              <input type="hidden" name="latitude" id="latitude">
              <input type="hidden" name="longitude" id="longitude">
            </div>
            <div class="form-row">
              <div class="input-box">
                <label for="name">Họ và Tên:</label>
                <input type="text" id="name" name="name" value="{{ auth()->user()->username }}" required>
              </div>
              <div class="input-box">
                <label for="phone">Số Điện Thoại:</label>
                <input type="number" class="input-phone" id="phone" name="phone" value="{{ auth()->user()->phone }}" required>
              </div>
            </div>
            <div class="form-row">
              <div class="input-box">
                <label for="service_name">Loại dịch vụ:</label>
                <input type="text" id="service_name" name="service_name" readonly>
                <input type="hidden" id="service_id" name="service_id">
              </div>
              <div class="input-box">
                <label for="location">Vị trí hiện tại:</label>
                <input type="text" id="location" name="location">
              </div>
            </div>
            <div class="form-row">
              <div class="input-box">
                <span class="span-file">Hình ảnh (nếu có):</span>
                <label class="custom-file-label" for="file-image">
                  Choose file
                  <input type="file" class="custom-file-image" id="file-image" name="file-name" style="display: none;">
                </label>
                <input type="hidden" id="file-name" name="photo">
              </div>
              <div class="input-box">
                <label for="service_price">Giá:</label>
                <input type="text" id="service_price" name="service_price" readonly>
              </div>
            </div>
            <div class="form-row">
              <div class="full-row">
                <label for="issue">Mô Tả Sự Cố:</label>
                <textarea id="issue" name="description" rows="4" required></textarea>
              </div>
            </div>
            <div class="form-actions">
              <button class="btn-submit" id="btn-send-request" type="submit">Gửi yêu cầu</button>
              <button class="btn-close" type="button" id="close-popup">Đóng</button>
            </div>
          </form>
      </div>
    </div>
    <div id="overlay"></div>
    <div id="map"></div>
  </div>

  @endsection
  @push('dashboard')
  <script>
    // File image
    document.getElementById('file-image').addEventListener('change', function() {
      var fileName = this.files[0] ? this.files[0].name : "Choose file";
      var label = document.querySelector('.custom-file-label');
      var hiddenInput = document.getElementById('file-name'); // Truy cập đến input ẩn

      // Cập nhật nội dung của label và giá trị của input ẩn
      label.innerHTML = fileName || "Choose file";
      hiddenInput.value = fileName; // Lưu tên file vào input ẩn
    });

    //Toggle
    const toggleServiceIcon = document.getElementById('toggleServiceIcon');
    const toggleRescueIcon = document.getElementById('toggleRescueIcon');
    const formService = document.getElementById('form-service');
    const formRescue = document.getElementById('form-rescue');
    const iconService = document.getElementById('iconService');
    const iconTechnician = document.getElementById('iconTechnician');

    toggleServiceIcon.addEventListener('click', function() {
      // console.log('>>>>>>toggleServiceIcon');
      if (formService.classList.contains('hidden')) {
        formService.classList.remove('hidden');
      } else {
        formService.classList.add('hidden');
      }
    });

    iconService.addEventListener('click', function() {
      if (formService.classList.contains('hidden')) {
        formService.classList.remove('hidden');
      }
    });

    document.addEventListener('DOMContentLoaded', function() {
      const now = new Date();

      // Chuyển đổi thời gian sang múi giờ Việt Nam
      const localTime = now.toLocaleString("en-US", {
        timeZone: "Asia/Ho_Chi_Minh"
      });

      // Chuyển đổi chuỗi thời gian sang đối tượng Date để format theo mong muốn
      const vietnamTime = new Date(localTime);

      // Định dạng lại thành chuỗi theo format "YYYY-MM-DD HH:MM:SS"
      const formattedTime = vietnamTime.getFullYear() + "-" +
        ("0" + (vietnamTime.getMonth() + 1)).slice(-2) + "-" +
        ("0" + vietnamTime.getDate()).slice(-2) + " " +
        ("0" + vietnamTime.getHours()).slice(-2) + ":" +
        ("0" + vietnamTime.getMinutes()).slice(-2) + ":" +
        ("0" + vietnamTime.getSeconds()).slice(-2);

      document.getElementById('requested_at').value = formattedTime;
    });

    $(document).ready(function() {
      $('#service_type').on('change', function() {
        var serviceType = $(this).val();

        if (serviceType) {
          $('#service-form-price').prop('disabled', false);
        } else {
          $('#service-form-price').prop('disabled', true);
        }
      });

    })
  </script>
  @endpush