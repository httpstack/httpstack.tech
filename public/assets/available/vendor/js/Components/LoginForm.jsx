function LoginForm() {
    const [username, setUsername] = React.useState('');
    const [password, setPassword] = React.useState('');
    const [passwordStrength, setPasswordStrength] = React.useState(0); // 0: weak, 1: medium, 2: strong
    const [submitted, setSubmitted] = React.useState(false);

    const minLength = 8;
    const hasNumber = /[0-9]/;
    const hasUpper = /[A-Z]/;
    const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;

    const validatePassword = (pwd) => {
        let score = 0;
        if (pwd.length >= minLength) score++;
        if (hasNumber.test(pwd)) score++;
        if (hasUpper.test(pwd)) score++;
        if (hasSpecial.test(pwd)) score++;

        if (score === 4) {
            setPasswordStrength(2); // Strong
        } else if (score >= 2) {
            setPasswordStrength(1); // Medium
        } else {
            setPasswordStrength(0); // Weak
        }
    };

    const isPasswordValid = () => {
        return (
            password.length >= minLength &&
            hasNumber.test(password) &&
            hasUpper.test(password) &&
            hasSpecial.test(password)
        );
    };

    const handleSubmit = (event) => {
        event.preventDefault();
        if (isPasswordValid()) {
            setSubmitted(true);
            // In a real application, you would send this to a server
            console.log('Login attempt:', { username, password });
        } else {
            alert('Please ensure your password meets all criteria.');
        }
    };

    const getStrengthMeterClass = () => {
        if (password.length === 0) return '';
        if (passwordStrength === 2) return 'strength-green';
        if (passwordStrength === 1) return 'strength-yellow';
        return 'strength-red';
    };

    if (submitted) {
        return (
            <div className="login-container">
                <h2>Login Successful! 🎉</h2>
                <div className="success-message">
                    <p>Welcome, {username}!</p>
                    <p>You submitted: {password.split('').map(() => '*').join('')}</p> {/* Mask password */}
                    <p>This demonstrates the form submission handling on the same page.</p>
                </div>
                <button onClick={() => setSubmitted(false)}>Go Back</button>
            </div>
        );
    }

    return (
        <div className="login-container">
            <h2>Login</h2>
            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label htmlFor="username">Username:</label>
                    <input
                        type="text"
                        id="username"
                        value={username}
                        onChange={(e) => setUsername(e.target.value)}
                        required
                    />
                </div>
                <div className="form-group">
                    <label htmlFor="password">Password:</label>
                    <input
                        type="password"
                        id="password"
                        value={password}
                        onChange={(e) => {
                            setPassword(e.target.value);
                            validatePassword(e.target.value);
                        }}
                        required
                    />
                    <div className={`password-strength-meter ${getStrengthMeterClass()}`}></div>
                    {password.length > 0 && !isPasswordValid() && (
                        <div className="error">
                            Password must:
                            <ul>
                                <li style={{ color: password.length >= minLength ? 'green' : '#e74c3c' }}>Be at least {minLength} characters</li>
                                <li style={{ color: hasNumber.test(password) ? 'green' : '#e74c3c' }}>Contain a number</li>
                                <li style={{ color: hasUpper.test(password) ? 'green' : '#e74c3c' }}>Contain an uppercase letter</li>
                                <li style={{ color: hasSpecial.test(password) ? 'green' : '#e74c3c' }}>Contain a special character</li>
                            </ul>
                        </div>
                    )}
                </div>
                <button type="submit" disabled={!isPasswordValid()}>Login</button>
            </form>
        </div>
    );
}

ReactDOM.render(
    <LoginForm />,
    document.getElementById('root')
);