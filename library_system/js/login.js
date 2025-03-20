document.getElementById('login-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const loginInput = document.getElementById('login_input').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');

    try {
        const response = await axios.post('/coc_lib/api/login.php', {
            user_email: loginInput,
            user_password: password
        });

        if (response.data.status === 'success') {
            alert('Login successful!');
            window.location.href = response.data.redirect; // Redirects based on user type
        } else {
            errorMessage.textContent = response.data.message || 'Invalid credentials!';
            errorMessage.style.display = 'block';
        }
    } catch (error) {
        console.error('Login error:', error);
        errorMessage.textContent = 'Something went wrong. Please try again.';
        errorMessage.style.display = 'block';
    }
});
