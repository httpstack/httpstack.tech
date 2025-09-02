class LoginForm extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.shadowRoot.innerHTML = `
            <style>
                .form-container {
                    max-width: 350px;
                    margin: 40px auto;
                    padding: 32px 24px;
                    background: #fff;
                    border-radius: 12px;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
                    font-family: 'Segoe UI', Arial, sans-serif;
                    animation: fadeIn 0.7s;
                }
                @keyframes fadeIn { from { opacity: 0; transform: translateY(30px);} to { opacity: 1; transform: none; } }
                h2 { text-align: center; margin-bottom: 18px; color: #2c3e50; }
                label { display: block; margin: 12px 0 4px; font-weight: 500; }
                input, select {
                    width: 100%; padding: 10px; margin-bottom: 16px;
                    border: 1px solid #ccc; border-radius: 6px; font-size: 1em;
                    transition: border-color 0.2s;
                }
                input:focus, select:focus { border-color: #3498db; outline: none; }
                .error { color: #e74c3c; font-size: 0.95em; margin-bottom: 8px; }
                button {
                    width: 100%; padding: 12px; background: #3498db; color: #fff;
                    border: none; border-radius: 6px; font-size: 1.1em; font-weight: 600;
                    cursor: pointer; transition: background 0.2s;
                }
                button:hover { background: #217dbb; }
            </style>
            <div class="form-container">
                <h2>Login</h2>
                <form id="loginForm" method="POST">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autocomplete="username">
                    <div class="error" id="emailError"></div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                    <div class="error" id="passwordError"></div>
                    <input type="submit" value="Login" onclick="this.form.submit();"/>
                </form>
            </div>
        `;
    }
    connectedCallback() {
        const $ = window.jQuery;
        const shadow = $(this.shadowRoot);
        const form = shadow.find('#loginForm');
        form.on('submit', function (e) {
            alert("submittin");
            e.preventDefault();
            let valid = true;
            const email = form.find('#email').val();
            const password = form.find('#password').val();
            const emailError = shadow.find('#emailError');
            const passwordError = shadow.find('#passwordError');
            emailError.text('');
            passwordError.text('');
            // Email validation
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                emailError.text('Please enter a valid email address.');
                valid = false;
            }
            // Password validation
            if (!/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*?+_-]).{8,16}$/.test(password)) {
                passwordError.text('Password must be 8-16 chars, include upper/lowercase, number, and special (!@#$%^&*?+_-).');
                valid = false;
            }
            if (valid) {
                alert("valid");
                $.post('/login', {
                    email: email,
                    password: password,
                    role: form.find('#role').val()
                }, function (resp) {
                    // handle response, show success/error, etc.
                });
            }
        });
    }
}
customElements.define('login-form', LoginForm);