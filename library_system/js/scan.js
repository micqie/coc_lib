let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
const TIMEOUT_DURATION = 60; // 1 minute in seconds
const COOLDOWN_DURATION = 60; // 1 minute cooldown
let activeUsers = new Map(); // Store active users and their timeouts
let cooldownUsers = new Map(); // Store users in cooldown
let cooldownIntervals = new Map(); // Store cooldown intervals

function showMessage(message, type = 'info', countdown = null) {
    const messageContainer = document.getElementById('messageContainer');
    const messageElement = document.createElement('div');
    messageElement.className = 'message';

    if (countdown) {
        // Create countdown display
        const countdownSpan = document.createElement('span');
        messageElement.textContent = 'Cannot timeout yet. Please wait ';
        messageElement.appendChild(countdownSpan);

        // Start countdown
        let timeLeft = countdown;
        const updateCountdown = () => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownSpan.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateCountdown, 1000);
            }
        };
        updateCountdown();

        // fade out
        setTimeout(() => {
            messageElement.classList.add('fade-out');
            setTimeout(() => {
                messageElement.remove();
            }, 500);
        }, 5000);
    } else {
        messageElement.textContent = message;
    }

    messageContainer.appendChild(messageElement);

    // Fade out after 5 seconds if not a countdown message
    if (!countdown) {
        setTimeout(() => {
            messageElement.classList.add('fade-out');
            setTimeout(() => {
                messageElement.remove();
            }, 500);
        }, 5000);
    } else {
        // For countdown messages, remove after countdown finishes
        setTimeout(() => {
            messageElement.classList.add('fade-out');
            setTimeout(() => {
                messageElement.remove();
            }, 500);
        }, (countdown + 1) * 1000);
    }
}

function showCooldownMessage(userId) {
    const message = document.getElementById('cooldownMessage');
    const timer = document.getElementById('cooldownTimer');
    message.style.display = 'block';

    let timeLeft = COOLDOWN_DURATION;
    const interval = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(interval);
            message.style.display = 'none';
            cooldownUsers.delete(userId);
            cooldownIntervals.delete(userId);
            showMessage('You can now scan again.');
        } else {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timer.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
        timeLeft--;
    }, 1000);

    cooldownIntervals.set(userId, interval);
}

function createUserCard(userData, isTimeout = false) {
    const cardId = `user-${Date.now()}`;
    const card = document.createElement('div');
    card.className = 'card user-card';
    card.id = cardId;
    card.setAttribute('data-user-id', userData.user_schoolId);

    const timeIn = userData.time_in || new Date().toLocaleTimeString();
    const timeOut = userData.time_out || '';

    card.innerHTML = `
        <div class="card-header ${isTimeout ? 'bg-danger' : 'bg-success'} text-white">
            <h4 class="mb-0">User Details</h4>
        </div>
        <div class="card-body">
            <div class="user-info">
                <p><strong>Name:</strong> ${userData.user_firstname} ${userData.user_middlename || ''} ${userData.user_lastname} ${userData.user_suffix || ''}</p>
                <p><strong>Email:</strong> ${userData.user_email}</p>
                <p><strong>Contact:</strong> ${userData.user_contact}</p>
                <p><strong>Department:</strong> ${userData.department_name || 'N/A'}</p>
                <p><strong>Course:</strong> ${userData.course_name || 'N/A'}</p>
                <p><strong>Time In:</strong> ${timeIn}</p>
                <p><strong>Time Out:</strong> <span class="countdown">${isTimeout ? timeOut : ''}</span></p>
            </div>
        </div>
    `;

    document.getElementById('activeUsers').appendChild(card);

    if (!isTimeout) {
        // Start countdown for this user
        const countdownElement = card.querySelector('.countdown');
        const timeoutId = setTimeout(() => {
            card.classList.add('timeout');
            countdownElement.textContent = new Date().toLocaleTimeString();
            cooldownUsers.set(userData.user_schoolId, Date.now());
            showCooldownMessage(userData.user_schoolId);

            // Fade out and remove the card after timeout
            setTimeout(() => {
                card.classList.add('fade-out');
                setTimeout(() => {
                    card.remove();
                }, 500);
            }, 5000);
        }, TIMEOUT_DURATION * 1000);

        // Store the timeout ID
        activeUsers.set(cardId, timeoutId);
    }

    return cardId;
}

// Start the scanner immediately
Instascan.Camera.getCameras().then(function (cameras) {
    if (cameras.length > 0) {
        scanner.start(cameras[0]);
    } else {
        console.error('No cameras found.');
        showMessage('Camera not detected. Please check your device settings.');
    }
}).catch(function (e) {
    console.error(e);
    showMessage('Unable to access camera. Please check your device permissions.');
});

async function handleScan(schoolId) {
    // Check if user is in cooldown
    if (cooldownUsers.has(schoolId)) {
        const timeLeft = Math.ceil((COOLDOWN_DURATION - (Date.now() - cooldownUsers.get(schoolId)) / 1000));
        showMessage(`Please wait ${timeLeft} seconds before scanning again.`);
        return;
    }

    try {
        const response = await axios.post('../api/time_in.php', {
            user_schoolId: schoolId
        });

        if (response.data.user_data) {
            if (response.data.is_timeout) {
                // Handle timeout
                const card = createUserCard(response.data.user_data, true);
                showMessage('Time-out successful!');

                // Fade out the card after timeout
                setTimeout(() => {
                    const cardElement = document.getElementById(card);
                    if (cardElement) {
                        cardElement.classList.add('fade-out');
                        setTimeout(() => {
                            cardElement.remove();
                        }, 500);
                    }
                }, 5000);
            } else {
                // Handle time-in
                const card = createUserCard(response.data.user_data);
                showMessage('Time-in successful!');

                // Fade out the card after successful time-in
                setTimeout(() => {
                    const cardElement = document.getElementById(card);
                    if (cardElement) {
                        cardElement.classList.add('fade-out');
                        setTimeout(() => {
                            cardElement.remove();
                        }, 500);
                    }
                }, 5000);
            }
        }
    } catch (error) {
        if (error.response?.data?.is_early_timeout) {
            // Handle early timeout attempt with live countdown
            const remainingSeconds = error.response.data.remaining_seconds;
            showMessage('', 'info', remainingSeconds);
        } else {
            showMessage('Unable to process time-in. Please try again.');
        }
    }
}

scanner.addListener('scan', function (content) {
    handleScan(content);
});

// Manual entry fallback
const schoolIdInput = document.getElementById('user_schoolId');
schoolIdInput.addEventListener('input', function (e) {
    const schoolId = e.target.value.trim();

    if (schoolId) {
        handleScan(schoolId);
        schoolIdInput.value = '';
    }
});

// Add this at the beginning of your script section
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const dateString = now.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    document.getElementById('clock').textContent = timeString;
    document.getElementById('date').textContent = dateString;
}

// Update clock every second
setInterval(updateClock, 1000);
updateClock(); // Initial call