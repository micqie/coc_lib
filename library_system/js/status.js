function checkTimeOut(userId) {
    fetch(`check_timein.php?user_schoolId=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                let timeLeft = 60 - data.time_difference;
                if (timeLeft > 0) {
                    alert(`Time Out Invalid! Please wait ${timeLeft} seconds`);
                } else {
                    proceedWithTimeOut(userId);
                }
            } else if (data.status === "no_record") {
                alert("No active time-in found. Please time in first.");
            }
        })
        .catch(error => console.error("Error fetching status:", error));
}

function proceedWithTimeOut(userId) {
    fetch("insert.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ text: userId })
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => console.error("Error:", error));
}

// Example usage (replace with actual user ID)
document.getElementById("timeOutButton").addEventListener("click", function() {
    let userId = document.getElementById("userInput").value;
    checkTimeOut(userId);
});
