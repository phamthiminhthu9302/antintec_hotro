console.log("Map here!");

var map = L.map("map").setView([10.8231, 106.6297], 13); // Mặc định là HCM city

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);

let markers = [];
let services = [];
let userLat, userLon;
let allServices = []; // Danh sách toàn bộ dịch vụ từ server

function getLocation() {
    console.log(">>>>getlocation");

    return new Promise((resolve, reject) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const userLat = position.coords.latitude;
                    const userLon = position.coords.longitude;
                    // console.log("User's location: ", userLat, userLon);

                    sendLocationToServer(userLat, userLon);
                    getCurrentAddress(userLat, userLon);
                    resolve({ userLat, userLon });
                },
                function (error) {
                    reject(error);
                }
            );
        } else {
            reject(new Error("Geolocation is not supported by this browser!"));
        }
    });
}

function getCurrentAddress(userLat, userLon) {
    // console.log(">>>get current address");
    var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${userLat}&lon=${userLon}&zoom=18&addressdetails=1`;

    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            // console.log("Phản hồi từ API:", data);
            var display_name = data.display_name;
            var addressParts = display_name.split(",").slice(0, -2);
            var fullAddress = addressParts.join(",");
            var latitude = data.lat;
            var longitude = data.lon;

            document.getElementById("location").value = fullAddress;
            document.getElementById("latitude").value = parseFloat(latitude);
            document.getElementById("longitude").value = parseFloat(longitude);
        })
        .catch((error) => {
            console.error("Lỗi khi lấy địa chỉ:", error);
        });
}

function displayListServices(allServices) {
    console.log(">>>>displayListServices");
    console.log("Initial data:", allServices);
    const serviceList = document.getElementById("service-list");

    // Kiểm tra xem data có phải là mảng và không rỗng hay không
    if (!Array.isArray(allServices) || allServices.length === 0) {
        // Nếu không có dữ liệu, hiển thị thông báo
        serviceList.innerHTML = "Không tìm thấy dịch vụ nào.";
        serviceList.classList.add("notification");
        return;
    }

    allServices.forEach((service) => {
        const listItemService = document.createElement("div");
        listItemService.innerHTML = createItemService(service);
        serviceList.appendChild(listItemService);
    });
}

function createItemService(service) {
    console.log(">>>>service", service);
    const servicePrice = parseFloat(service.price);
    const formattedPrice = new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
    }).format(servicePrice);

    return `<li id='service-card'>
        <div class='location-title'>
            <label>
                <p class='location-title-service' data-serviceId="${service.service_id}">Dịch vụ: ${service.name}</p>
            </label>
        </div>
        <div class='location-time'>
            <p class='location-title-text'>Mô tả: ${service.description}</p>
        </div>
        <div class='location-technicianId' data-technicianId="${allServices.technician.technician_id}">
            <p class='location-technician-name' data-technicianName="${allServices.technician.technician_name}"></p>
        </div>
        <div class='location-time'>
            <p class='location-title-text'>Giá: <span class="formatted-price">${formattedPrice}</p>
        </div>
        <button>Đặt</button>
        <hr>
    </li>`;
}

// Biến để lưu marker hiện tại
let currentMarker = null;

function handleEventItemService(allServices) {
    document
        .getElementById("service-list")
        .addEventListener("click", function (event) {
            // Kiểm tra xem người dùng có click vào một phần tử LI không
            const listItem = event.target.closest("li");
            // console.log(">>>>>listItem", listItem.children);
            if (listItem) {
                const serviceName = listItem.children[0].textContent
                    .split(":")[1]
                    .trim();
                const servicePrice = listItem.children[3].textContent
                    .split(":")[1]
                    .trim()
                    .replace("₫", "")
                    .trim();

                const dataServiceId = document.querySelector(
                    ".location-title-service"
                );
                const dataTechnician = document.querySelector(
                    ".location-technician-name"
                );
                const dataTechnicianId = document.querySelector(
                    ".location-technicianId"
                );

                const serviceId = dataServiceId.getAttribute("data-serviceid");
                const technicianName = dataTechnician.getAttribute(
                    "data-technicianName"
                );
                const technicianId =
                    dataTechnicianId.getAttribute("data-technicianId");

                document.getElementById("service_name").value = serviceName;
                document.getElementById("service_price").value = servicePrice;
                document.getElementById("technicianId").value = technicianName;
                document.getElementById("technician_id").value =
                    parseInt(technicianId);
                document.getElementById("service_id").value =
                    parseInt(serviceId);
                document.getElementById("request-popup").style.display =
                    "block";
            }
        });

    // Đóng popup khi nhấn nút "Đóng"
    document
        .getElementById("close-popup")
        .addEventListener("click", function () {
            document.getElementById("request-popup").style.display = "none";
        });
}

// Hiển thị popup và làm mờ nền
function showPopup() {
    document.getElementById("request-popup").style.display = "block";
    document.getElementById("overlay").style.display = "block";
    document.body.classList.add("popup-active");
}

// Ẩn popup và bỏ làm mờ nền
function hidePopup() {
    document.getElementById("request-popup").style.display = "none";
    document.getElementById("overlay").style.display = "none";
    document.body.classList.remove("popup-active");
}

document.getElementById("close-popup").addEventListener("click", hidePopup);

function filterFormServices() {
    const selectedPrice = document.getElementById("services_price").value;
    const input = document.getElementById("service-type");
    const filter = input.value.toLowerCase();
    const serviceList = document.getElementById("service-list"); // Lấy danh sách dịch vụ
    // Xóa nội dung danh sách dịch vụ cũ
    serviceList.innerHTML = "";

    // Ẩn danh sách nếu không có từ khóa nhập
    if (!filter && !selectedPrice) {
        serviceList.style.display = "none";
        return;
    }

    let services = allServices.services;

    let filteredServices = services.filter(
        (service) => service.name.toLowerCase().includes(filter) // Lọc theo tên dịch vụ
    );

    // Nếu combobox giá đã được chọn, tiếp tục lọc theo giá
    if (selectedPrice === "under_200k") {
        filteredServices = filteredServices.filter(
            (service) => parseFloat(service.price) < 200000
        );
    } else if (selectedPrice === "200k_to_500k") {
        filteredServices = filteredServices.filter(
            (service) =>
                parseFloat(service.price) >= 200000 &&
                parseFloat(service.price) <= 500000
        );
    } else if (selectedPrice === "over_500k") {
        filteredServices = filteredServices.filter(
            (service) => parseFloat(service.price) > 500000
        );
    }

    // Hiển thị danh sách đã lọc
    if (filteredServices.length === 0) {
        serviceList.innerHTML =
            "Không tìm thấy dịch vụ nào với các tiêu chí đã chọn";
        serviceList.classList.add("error-service");
        serviceList.style.display = "block";
    } else {
        serviceList.style.display = "block";
        displayListServices(filteredServices);
    }
}

document
    .getElementById("service-type")
    .addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            filterFormServices();
        }
    });

function sendLocationToServer(userLat, userLon) {
    // console.log(">>>sendLocationToServer", userLat, userLon);
    var latitude = userLat;
    var longitude = userLon;
    var role = document.getElementById("role").value;
    var userId = document.getElementById("userId").value;
    createMarker(userId, role, latitude, longitude);
    $.ajax({
        type: "POST",
        url: "/save-location",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            id: userId,
            role: role,
            latitude: latitude,
            longitude: longitude,
        },
        success: function (response) {
            console.log(response);
        },
        error: function (xhr, status, error) {
            console.log(status);
        },
    });
    var id = userId;
    axios.post(`/getServices/${id}/${role}/${latitude}/${longitude}`)
        .then(async response => {
            console.log("Update status response:", response);
            console.log("Response services:", response.data.services);
            allServices = response.data;
            displayListServices(allServices);
            handleEventItemService(allServices); // Xử lý khi click vào dịch vụ
        }).catch(error => {
            console.log("Update status error:", status);
        });

}

var technicianIcon = L.icon({
    iconUrl: "https://cdn-icons-png.flaticon.com/256/5025/5025140.png",
    iconSize: [40, 40],
});

var customerIcon = L.icon({
    iconUrl:
        "https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png",
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
});

function createMarker(userId, role, latitude, longitude) {
    var position = [latitude, longitude];

    const icon = role === "technician" ? technicianIcon : customerIcon;

    var marker = L.marker(position, { icon: icon }).addTo(map);
    marker.bindPopup(`Id:${userId} - Role: ${role}`);
}

Pusher.logToConsole = true;
var pusher = new Pusher("b5f44c6c2b7e9df067d7", {
    cluster: "ap1",
});

var technicianMarkers = {};
let technicianDistances = [];

var channeltechnician_location = pusher1.subscribe("technician-location");
channeltechnician_location.bind("TechnicianLocationUpdated", function (data) {
    console.log("->>>Technician location updated:", data);
    updateTechnicianMarker(data);
});

function updateTechnicianMarker(data) {
    console.log(">>>update");
    var position = [data.latitude, data.longitude];
    const icon = technicianIcon;

    if (technicianMarkers[data.technicianId]) {
        technicianMarkers[data.technicianId].setLatLng(position);
    } else {
        technicianMarkers[data.technicianId] = L.marker(position, {
            icon: icon,
        }).addTo(map);
    }
}

document
    .getElementById("services_price")
    .addEventListener("change", function () {
        filterFormServices();
    });

document
    .getElementById("service-form")
    .addEventListener("submit", function (event) {
        event.preventDefault();
        filterFormServices();
    });

const formRequest = document.getElementById("request-form");
formRequest.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("/save-request", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(
                    "Network response was not ok " + response.statusText
                );
            }
            return response.json(); // Trả về JSON
        })
        .then((data) => {
            console.log("Raw response from server:", data); // Kiểm tra phản hồi
        })
        .catch((error) => {
            console.error("Error:", error);
        });
});

const submitButton = document.getElementById("btn-send-request");
submitButton.addEventListener("click", function () {
    document.getElementById("request-popup").style.display = "none";
    alert("Vui lòng đợi kỹ thuật viên xử lý!");
});

window.onload = function () {
    getLocation();
};
