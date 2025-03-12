document.addEventListener("DOMContentLoaded", function () {
    let scanner = new Instascan.Scanner({ video: document.getElementById("preview") });

    scanner.addListener("scan", function (content) {
        document.getElementById("text").value = content; // Display scanned QR code

        processScan(content); // Call function to handle time-in/time-out logic
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

// Function to process the scanned QR code
function processScan(userId) {
    axios.post("http://localhost/coc_lib/api/insert.php", { text: userId })
        .then(response => {
            let messageDiv = document.getElementById("message");
            messageDiv.textContent = response.data.message;

            if (response.data.status === "success") {
                messageDiv.className = "alert alert-success";
                loadLogs(); // Refresh logs after successful scan
            } else {
                messageDiv.className = "alert alert-danger";
            }

            messageDiv.classList.remove("d-none");

            // Hide message after 3 seconds
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
}

