console.log("form technician");
document
    .getElementById("add-skill")
    .addEventListener("click", function (event) {
        event.preventDefault();

        const technicianId = document.getElementById("technician_form_id").value;
        const serviceId = document.getElementById("service_form_id").value;

        if (!technicianId || !serviceId) {
            alert("Please select both technician and service.");
            return;
        }

        const formData = new FormData();
        formData.append("technician_id", technicianId);
        formData.append("service_id", serviceId);
        formData.append(
            "_token",
            document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content")
        ); // Sử dụng meta tag cho CSRF token

        fetch("/technician-service", {
            method: "POST",
            body: formData,
        })
            .then((response) => {
                if (!response.ok) {
                    throw response;
                }
                return response.json();
            })
            .then((data) => {
                if (data.error) {
                    alert(data.error); 
                } else {
                    alert(data.message);  
                }
            })
            .catch((error) => {
                error.json().then((err) => {
                    alert("Error: " + err.error);
                });
            });
    });

function deleteService(serviceId) {
    let id = parseInt(serviceId);
    if (confirm("Bạn chắc chắn muốn xóa dịch vụ này chứ?")) {
        fetch(`/technician-service/${id}`, {
            // Đường dẫn API để xóa dịch vụ
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"), // CSRF token
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                alert(data.message);  
                location.reload();  
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Error deleting the service.");  
            });
    }
}
