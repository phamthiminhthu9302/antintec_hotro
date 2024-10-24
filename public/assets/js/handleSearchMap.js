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
    console.log("check allServices:", typeof allServices);
    const serviceList = document.getElementById("service-list");
    serviceList.innerHTML = "";
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
        <div class='location-technicianId' data-technicianId="${service.technician_id}">
            <p class='location-technician-name'>Kỹ thuật viên:${service.technician_name}</p>
        </div>
        <div class='location-time'>
            <p class='location-title-text'>Giá: <span class="formatted-price">${formattedPrice}</p>
        </div>
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
            // if (listItem) {
            //     const serviceName = listItem.children[0].textContent
            //         .split(":")[1]
            //         .trim();
            //     const servicePrice = listItem.children[3].textContent
            //         .split(":")[1]
            //         .trim()
            //         .replace("₫", "")
            //         .trim();

            //     const dataServiceId = document.querySelector(
            //         ".location-title-service"
            //     );
            //     const dataTechnician = document.querySelector(
            //         ".location-technician-name"
            //     );
            //     const dataTechnicianId = document.querySelector(
            //         ".location-technicianId"
            //     );

            //     const serviceId = dataServiceId.getAttribute("data-serviceid");
            //     const technicianName = dataTechnician.getAttribute(
            //         "data-technicianName"
            //     );
            //     const technicianId =
            //         dataTechnicianId.getAttribute("data-technicianId");

            //     document.getElementById("service_name").value = serviceName;
            //     document.getElementById("service_price").value = servicePrice;
            //     document.getElementById("technicianId").value = technicianName;
            //     document.getElementById("technician_id").value =
            //         parseInt(technicianId);
            //     document.getElementById("service_id").value =
            //         parseInt(serviceId);
            // }
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

function sendLocationToServer(userLat, userLon) {
    // console.log(">>>sendLocationToServer", userLat, userLon);
    var latitude = userLat;
    var longitude = userLon;
    var role = document.getElementById("role").value;
    var userId = document.getElementById("userId").value;
    createMarker(userId, role, latitude, longitude);
    //Lưu tọa độ của kỹ thuật viên
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
    sendFormService(latitude, longitude);
}

function sendFormService(latitude, longitude) {
    const formSearchService = document.getElementById("service-form");
    formSearchService.addEventListener("submit", function (e) {
        e.preventDefault();
        let lat = latitude;
        let lon = longitude;
        const formData = new FormData(this);

        // Thêm tọa độ latitude và longitude vào formData
        formData.append("latitude", lat);
        formData.append("longitude", lon);

        fetch("/filterServices", {
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
                allServices = data.listTechnicians;
                displayListServices(allServices);
                // handleEventItemService(allServices); // Xử lý khi click vào dịch vụ
            })
            .catch((error) => {
                console.error("Error:", error);
            });
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

var channel = pusher.subscribe("technician-location");
channel.bind("TechnicianLocationUpdated", function (data) {
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

document.getElementById("service_type").addEventListener("change", function () {
    var priceSelect = document.getElementById("service-form-price");
    priceSelect.innerHTML = ""; // Xóa tất cả các tùy chọn trước

    // Lấy giá của dịch vụ đã chọn
    var selectedOption = this.options[this.selectedIndex];
    var selectedPrice = selectedOption.getAttribute("data-price");
    var selectedType = selectedOption.getAttribute("data-name");
    var selectedServiceId = selectedOption.getAttribute("data-service-id");

    const servicePrice = parseFloat(selectedPrice);
    const serviceId = parseInt(selectedServiceId);
    const formattedPrice = new Intl.NumberFormat("vi-VN").format(servicePrice);

    document.getElementById("service_price").value = formattedPrice;
    document.getElementById("service_name").value = selectedType;
    document.getElementById("service_id").value = serviceId;

    if (selectedPrice) {
        var option = document.createElement("option");
        option.value = selectedOption.value;
        option.textContent =
            "Giá: " +
            new Intl.NumberFormat("vi-VN").format(selectedPrice) +
            " VND";
        priceSelect.appendChild(option);
        priceSelect.removeAttribute("disabled");
    } else {
        priceSelect.setAttribute("disabled", true);
    }
});

document.getElementById("close-popup").addEventListener("click", hidePopup);

// Đóng popup khi nhấn nút "Đóng"
document.getElementById("close-popup").addEventListener("click", function () {
    document.getElementById("request-popup").style.display = "none";
});

document
    .getElementById("btn-book-now")
    .addEventListener("click", function (event) {

        document.getElementById("request-popup").style.display = "block";
    });

const formRequest = document.getElementById("request-form");
formRequest.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    //Lưu request
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
                    "Lỗi " + response.statusText
                );
            }
            return response.json();  
        })
        .then((data) => {
            console.log("Dữ liệu trả về từ server:", data);  
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
