console.log("Map here!");

// Khởi tạo bản đồ
var map = L.map("map").setView([10.8231, 106.6297], 13); // Mặc định là HCM city

// Thêm tile layer từ OpenStreetMap
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);

let markers = [];
let services = [];

fetch("/getServices")
    .then((response) => response.json())
    .then((data) => {
        services = data; // Lưu dữ liệu vào biến toàn cục
        displayServices(services); // Hiển thị dữ liệu lên form
        createPopupContent(services); //Tạo nội dung cho popup
        handleEventItemService(services); //Xử lý khi click vào dịch vụ
    })
    .catch((error) => console.error("Error:", error));

function createPopupContent(services) {
    return `
            <li id='service-card' data-lat="${services.latitude}" data-lon="${services.longitude}">
                <img class='location-photo' src='${services.photo}'/>
                <div class='location-title'>
                    <p class='location-title-text'>${services.name}</p><br>
                </div>
                <div class='location-address'>
                <img class="Liguzb" src="https://www.gstatic.com/images/icons/material/system_gm/1x/place_gm_blue_24dp.png">
                ${services.address}
                </div>
                <div class='location-address'>
                <img class="icon-phone" src="https://w7.pngwing.com/pngs/915/706/png-transparent-blue-call-icon-dialer-android-google-play-telephone-phone-blue-text-telephone-call.png">
                ${services.phone}
                </div>
                <div class='location-time'>
                <img class="Liguzb" src="//fonts.gstatic.com/s/i/googlematerialicons/history/v12/gm_blue-24dp/1x/gm_history_gm_blue_24dp.png">
                ${services.description}
                </div>
                <div class='location-desc'>
                <img class="Liguzb" src="//www.gstatic.com/images/icons/material/system_gm/1x/verified_user_gm_blue_24dp.png">
                <span class='location-desc-text'>Đã xác nhận </span>
                </div>
                <button>Đặt</button>
                <hr>
            </li>`;
}

// Hiển thị danh sách dịch vụ lên form
function displayServices(data) {
    const serviceList = document.getElementById("service-list");
    serviceList.innerHTML = ""; // Xóa dữ liệu cũ trước khi thêm mới

    // Xóa tất cả các marker cũ khỏi bản đồ
    markers.forEach((marker) => map.removeLayer(marker));
    markers = []; // Xóa dữ liệu marker cũ

    data.forEach((service) => {
        // Tạo một marker cho mỗi dịch vụ
        const marker = L.marker([service.latitude, service.longitude]).addTo(
            map
        );
        marker.bindPopup(createPopupContent(service));

        // Thêm marker vào danh sách marker để dễ quản lý
        markers.push(marker);

        //Tạo cấu trúc cho danh sách dịch vụ trên form
        const listItemService = document.createElement("div");
        listItemService.innerHTML = createPopupContent(service);
        serviceList.appendChild(listItemService);
    });
}

// Biến để lưu marker hiện tại
let currentMarker = null;

function handleEventItemService(data) {
    document
        .getElementById("service-list")
        .addEventListener("click", function (event) {
            // Kiểm tra xem người dùng có click vào một phần tử LI không
            const listItem = event.target.closest("li");
            if (listItem) {
                const lat = parseFloat(listItem.getAttribute("data-lat")); 
                const lon = parseFloat(listItem.getAttribute("data-lon"));
                // Cập nhật vị trí bản đồ mà không khởi tạo lại
                map.setView([lat, lon], 19);

                // Tìm dịch vụ được chọn từ danh sách data dựa trên lat và lon
                const selectedService = data.find((service) => {
                    return service.latitude == lat && service.longitude == lon;
                });

                // Nếu dịch vụ được chọn tồn tại
                if (selectedService) {
                    // Nếu marker đã tồn tại, chỉ cần cập nhật vị trí của nó
                    if (currentMarker) {
                        currentMarker.setLatLng([lat, lon]);
                        currentMarker.bindPopup(
                            createPopupContent(selectedService)
                        );
                    } else {
                        currentMarker = L.marker([lat, lon])
                            .addTo(map)
                            .bindPopup(createPopupContent(selectedService)); 
                    }

                    // Mở popup
                    currentMarker.openPopup();
                }

            }
        });
}

// Hàm lọc dịch vụ theo quốc gia hoặc các tiêu chí khác
function filterPrices() {
    const selectedPrice = document.getElementById("price").value;

    const filteredPrices = services.filter((service) => {
        return selectedPrice == service.price;
    });

    // Hiển thị các dịch vụ đã lọc
    displayServices(filteredPrices);
}

// document.getElementById("country").addEventListener("change", filterCountry);

function filterServices() {
    const selectedService = document.getElementById("service-type").value;

    // Lọc dịch vụ theo quốc gia (ví dụ)
    const filteredServices = services.filter((service) => {
        return selectedService == service.service_types_id;
    });

    // Hiển thị các dịch vụ đã lọc
    displayServices(filteredServices);
}

document.getElementById("service-form").addEventListener("submit", function (event) {
    event.preventDefault();
    filterServices();
    filterPrices();  
});