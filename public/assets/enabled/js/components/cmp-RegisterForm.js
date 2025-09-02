class RegisterForm extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.shadowRoot.innerHTML = `
            <style>
                .form-container { max-width: 350px; margin: 40px auto; padding: 32px 24px; background: #fff; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); font-family: 'Segoe UI', Arial, sans-serif; animation: fadeIn 0.7s;}
                @keyframes fadeIn { from { opacity: 0; transform: translateY(30px);} to { opacity: 1; transform: none; } }
                h2 { text-align: center; margin-bottom: 18px; color: #2c3e50; }
                label { display: block; margin: 12px 0 4px; font-weight: 500; }
                input, select { width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 6px; font-size: 1em; transition: border-color 0.2s;}
                input:focus, select:focus { border-color: #3498db; outline: none; }
                .error { color: #e74c3c; font-size: 0.95em; margin-bottom: 8px; }
                .strength-meter { height: 8px; width: 100%; background: #eee; border-radius: 4px; margin-bottom: 8px; overflow: hidden;}
                .strength-bar { height: 100%; width: 0; background: #e74c3c; transition: width 0.3s, background 0.3s;}
                button { width: 100%; padding: 12px; background: #27ae60; color: #fff; border: none; border-radius: 6px; font-size: 1.1em; font-weight: 600; cursor: pointer; transition: background 0.2s;}
                button:hover { background: #219150; }
            </style>
            <div class="form-container">
                <h2>Register</h2>
                <form id="registerForm">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autocomplete="username">
                    <div class="error" id="emailError"></div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password">
                    <div class="strength-meter"><div class="strength-bar" id="strengthBar"></div></div>
                    <div class="error" id="passwordError"></div>
                    <label for="confirm">Confirm Password</label>
                    <input type="password" id="confirm" name="confirm" required autocomplete="new-password">
                    <div class="error" id="confirmError"></div>
                    <label for="role">I am a:</label>
                    <select id="role" name="role">
                        <option value="Developer">Developer</option>
                        <option value="Employer">Potential Employer</option>
                        <option value="Customer">Potential Customer</option>
                    </select>
                    <button type="submit">Register</button>
                </form>
            </div>
        `;
    }
    connectedCallback() {
        const $ = window.jQuery;
        const shadow = $(this.shadowRoot);
        const form = shadow.find('#registerForm');
        const password = form.find('#password');
        const confirm = form.find('#confirm');
        const strengthBar = shadow.find('#strengthBar');
        password.on('input', function () {
            const val = password.val();
            let score = 0;
            if (val.length >= 8) score++;
            if (val.length >= 12) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[a-z]/.test(val)) score++;
            if (/\d/.test(val)) score++;
            if (/[!@#$%^&*?+_-]/.test(val)) score++;
            strengthBar.css('width', (score / 6 * 100) + '%');
            let color = '#e74c3c';
            if (score >= 5) color = '#27ae60';
            else if (score >= 3) color = '#f1c40f';
            strengthBar.css('background', color);
        });
        form.on('submit', function (e) {
            e.preventDefault();
            let valid = true;
            const email = form.find('#email').val();
            const pass = password.val();
            const conf = confirm.val();
            const emailError = shadow.find('#emailError');
            const passwordError = shadow.find('#passwordError');
            const confirmError = shadow.find('#confirmError');
            emailError.text('');
            passwordError.text('');
            confirmError.text('');
            // Email validation
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                emailError.text('Please enter a valid email address.');
                valid = false;
            }
            // Password validation
            if (!/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*?+_-]).{8,16}$/.test(pass)) {
                passwordError.text('Password must be 8-16 chars, include upper/lowercase, number, and special (!@#$%^&*?+_-).');
                valid = false;
            }
            // Confirm password
            if (pass !== conf) {
                confirmError.text('Passwords do not match.');
                valid = false;
            }
            if (valid) {
                $.post('/register', {
                    email: email,
                    password: pass,
                    role: form.find('#role').val()
                }, function (resp) {
                    // handle response, show success/error, etc.
                });
            }
        });
    }
}
customElements.define('register-form', RegisterForm);