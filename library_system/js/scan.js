document.addEventListener("DOMContentLoaded", function () {
    let scanner = new Instascan.Scanner({ video: document.getElementById("preview") });

    scanner.addListener("scan", function (content) {
        document.getElementById("text").value = content; // Display scanned QR code

        // Send data using Axios
        axios.post("http://localhost/api/insert.php", { text: content })
        .then(response => {
            let messageDiv = document.getElementById("message");

            if (response.data.status === "success") {
                messageDiv.textContent = response.data.message; // Show success message
                messageDiv.className = "alert alert-success"; 
                messageDiv.classList.remove("d-none"); 

                loadLogs(); 
            } else {
                messageDiv.textContent = response.data.message; // Show error message
                messageDiv.className = "alert alert-danger"; // Bootstrap error styling
                messageDiv.classList.remove("d-none"); // Show message
            }

            // Hide the message after 3 seconds
            setTimeout(() => {
                messageDiv.classList.add("d-none");
            }, 3000);
        })
        .catch(error => {
            console.error("Error inserting log:", error);
            let messageDiv = document.getElementById("message");
            messageDiv.textContent = "Error: Could not connect to the server.";
            messageDiv.className = "alert alert-danger";
            messageDiv.classList.remove("d-none");

            setTimeout(() => {
                messageDiv.classList.add("d-none");
            }, 3000);
        });
    });

    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            console.error("No cameras found.");
        }
    }).catch(function (e) {
        console.error(e);
    });
});
