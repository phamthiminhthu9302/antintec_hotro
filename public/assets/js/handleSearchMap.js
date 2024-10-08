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
let userLat, userLon;
let allServices = []; // Danh sách toàn bộ dịch vụ từ server
let servicesWithinRadius = []; // Danh sách dịch vụ đã lọc theo bán kính 10km
let nearbyServices = []; // Danh sách dịch vụ sau khi lọc theo loại hoặc giá

async function fetchServices() {
    try {
        const response = await fetch("/getServices");
        const services = await response.json();

        // Lưu trữ dịch vụ vào mảng nearbyServices
        allServices = services;

        // Đợi getLocation() hoàn thành trước khi gọi displayServices
        const location = await getLocation();
        userLat = location.userLat;
        userLon = location.userLon;

        servicesWithinRadius = getNearbyServices(allServices, userLat, userLon);
        displayServices(servicesWithinRadius, userLat, userLon); // Hiển thị dữ liệu lên form kèm vị trí
        createPopupContent(servicesWithinRadius); // Tạo nội dung cho popup
        handleEventItemService(servicesWithinRadius); // Xử lý khi click vào dịch vụ
    } catch (error) {
        console.error("Error:", error);
    }
}

// fetch("/getServices")
//     .then((response) => response.json())
//     .then((data) => {
//         services = data; // Lưu dữ liệu vào biến toàn cục
//         displayServices(services); // Hiển thị dữ liệu lên form
//         createPopupContent(services); //Tạo nội dung cho popup
//         handleEventItemService(services); //Xử lý khi click vào dịch vụ
//     })
//     .catch((error) => console.error("Error:", error));

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

//Hàm lấy vị trí hiện tại
function getLocation() {
    console.log(">>>>getlocation");

    return new Promise((resolve, reject) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const userLat = position.coords.latitude;
                    const userLon = position.coords.longitude;
                    console.log("User's location: ", userLat, userLon);
                    var circle = L.circle([userLat, userLon], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: 10
                      }).addTo(map)
                    // displayServices(services, userLat, userLon);
                    resolve({ userLat, userLon });
                },
                function (error) {
                    reject(error); // Xử lý khi xảy ra lỗi
                }
            );
        } else {
            reject(new Error("Geolocation is not supported by this browser!"));
        }
    });
}

function getNearbyServices(data, userLat, userLon) {
    let radius = 10;
    return data.filter((service) => {
        const serviceLat = service.latitude;
        const serviceLon = service.longitude;
        const distance = haversineDistance(
            userLat,
            userLon,
            serviceLat,
            serviceLon
        );
        return distance <= radius;
    });
}

// Hiển thị danh sách dịch vụ lên form
function displayServices(data, userLat = null, userLon = null) {
    console.log(">>>>displayServices");
    const serviceList = document.getElementById("service-list");
    serviceList.innerHTML = ""; // Xóa dữ liệu cũ trước khi thêm mới

    // Xóa tất cả các marker cũ khỏi bản đồ
    markers.forEach((marker) => map.removeLayer(marker));
    markers = []; // Xóa dữ liệu marker cũ

    if (userLat !== null && userLon !== null) {
        console.log("Initial data:", data);
        // servicesWithinRadius = getNearbyServices(data, userLat, userLon);
        // console.log("Nearby services after filtering:", servicesWithinRadius);
        if (data.length === 0) {
            console.log("No services to display");
            return; // Không có dịch vụ nào để hiển thị
        }

        // console.log(">>>servicesWithinRadius",servicesWithinRadius);

        data.forEach((service) => {
            // Tạo một marker cho mỗi dịch vụ
            const marker = L.marker([
                service.latitude,
                service.longitude,
            ]).addTo(map);

            marker.bindPopup(createPopupContent(service));

            // Thêm marker vào danh sách marker để dễ quản lý
            markers.push(marker);

            //Tạo cấu trúc cho danh sách dịch vụ trên form
            const listItemService = document.createElement("div");
            listItemService.innerHTML = createPopupContent(service);
            serviceList.appendChild(listItemService);
        });
    }
}

// Hàm tính khoảng cách Haversine
function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = ((lat2 - lat1) * Math.PI) / 180;
    const dLon = ((lon2 - lon1) * Math.PI) / 180;
    const a =
        0.5 -
        Math.cos(dLat) / 2 +
        (Math.cos((lat1 * Math.PI) / 180) *
            Math.cos((lat2 * Math.PI) / 180) *
            (1 - Math.cos(dLon))) /
            2;
    return R * 2 * Math.asin(Math.sqrt(a));
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

function filterFormServices() {
    const selectedPrice = document.getElementById("price").value;
    const selectedService = document.getElementById("service-type").value;

    if (typeof userLat === "undefined" || typeof userLon === "undefined") {
        console.error("Địa chỉ người dùng không có sẵn!");
        return;
    }

    if (!servicesWithinRadius || servicesWithinRadius.length === 0) {
        console.error("Không có dịch vụ nào gần đây!");
        return;
    }

    const servicesCopy = [...servicesWithinRadius];
    const priceSelect = document.getElementById("price");

    const filteredServices = servicesCopy.filter((service) => {
        let matchPrice = true;
        let matchServiceType = true;

        if (selectedService) {
            matchServiceType = parseInt(selectedService) === parseInt(service.service_types_id);
            priceSelect.disabled = false;
            if (selectedPrice) {
                matchPrice = parseFloat(selectedPrice) === parseFloat(service.price);
            }
        } else {
            priceSelect.disabled = true;
        }
        return matchPrice && matchServiceType; // Trả về dịch vụ thỏa mãn cả hai điều kiện
    });

    // Kiểm tra rằng filteredServices không phải là một mảng rỗng
    if (filteredServices.length > 0) {
        // console.log(">>>>check filteredServices: ", filteredServices);
        displayServices(filteredServices, userLat, userLon); // Hiển thị các dịch vụ đã lọc
    } else {
        alert("Không tìm thấy dịch vụ nào với các tiêu chí đã chọn.");
    }
}

document.getElementById("service-type").addEventListener("change", function () {
    filterFormServices();
});

document.getElementById("price").addEventListener("change", function () {
    filterFormServices();
});

document
    .getElementById("service-form")
    .addEventListener("submit", function (event) {
        event.preventDefault();
        filterFormServices();
    });

window.onload = function () {
    getLocation();
    fetchServices();
};
