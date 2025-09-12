
class CustomLoginForm extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });
  }

  connectedCallback() {
    this.render();
    this.setupEventListeners();
  }

  setupEventListeners() {
    const form = this.shadowRoot.querySelector('#login-form');
    const emailInput = this.shadowRoot.querySelector('#email');
    const passwordInput = this.shadowRoot.querySelector('#password');

    form.addEventListener('submit', (e) => this.handleSubmit(e));
    emailInput.addEventListener('input', () => this.validateEmail());
    passwordInput.addEventListener('input', () => this.validatePassword());
  }

  validateEmail() {
    const email = this.shadowRoot.querySelector('#email').value;
    const emailError = this.shadowRoot.querySelector('#email-error');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!email) {
      emailError.textContent = '';
      return false;
    }
    
    if (!emailRegex.test(email)) {
      emailError.textContent = 'Please enter a valid email address';
      return false;
    }
    
    emailError.textContent = '';
    return true;
  }

  validatePassword() {
    const password = this.shadowRoot.querySelector('#password').value;
    const passwordError = this.shadowRoot.querySelector('#password-error');
    const strengthMeter = this.shadowRoot.querySelector('#strength-meter');
    const strengthText = this.shadowRoot.querySelector('#strength-text');
    
    if (!password) {
      passwordError.textContent = '';
      strengthMeter.className = 'strength-meter';
      strengthText.textContent = '';
      return false;
    }

    // Password requirements
    const hasLength = password.length >= 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    const score = [hasLength, hasUpper, hasLower, hasNumber, hasSpecial].filter(Boolean).length;
    
    if (score < 4) {
      passwordError.textContent = 'Password must be at least 8 characters with uppercase, lowercase, number, and special character';
    } else {
      passwordError.textContent = '';
    }
    
    // Update strength meter
    if (score <= 2) {
      strengthMeter.className = 'strength-meter weak';
      strengthText.textContent = 'Weak';
    } else if (score <= 3) {
      strengthMeter.className = 'strength-meter medium';
      strengthText.textContent = 'Medium';
    } else {
      strengthMeter.className = 'strength-meter strong';
      strengthText.textContent = 'Strong';
    }
    
    return score >= 4;
  }

  handleSubmit(e) {
    e.preventDefault();
    
    const isEmailValid = this.validateEmail();
    const isPasswordValid = this.validatePassword();
    
    if (isEmailValid && isPasswordValid) {
      this.showToast('🎉 Login successful! Welcome back!', 'success');
    } else {
      this.showToast('❌ Please fix the errors above', 'error');
    }
  }

  showToast(message, type) {
    // Dispatch custom event to show toast in React app
    window.dispatchEvent(new CustomEvent('show-toast', {
      detail: { message, type }
    }));
  }

  render() {
    this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          width: 100%;
        }
        
        .form-container {
          background: rgba(15, 23, 42, 0.8);
          backdrop-filter: blur(10px);
          border: 1px solid rgba(148, 163, 184, 0.2);
          border-radius: 16px;
          padding: 2rem;
          box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .form-title {
          color: white;
          font-size: 1.5rem;
          font-weight: 700;
          margin-bottom: 1.5rem;
          text-align: center;
          background: linear-gradient(135deg, #8b5cf6, #ec4899);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          background-clip: text;
        }
        
        .form-group {
          margin-bottom: 1.5rem;
        }
        
        .form-label {
          display: block;
          color: #e2e8f0;
          font-weight: 500;
          margin-bottom: 0.5rem;
        }
        
        .form-input {
          width: 100%;
          padding: 0.75rem 1rem;
          background: rgba(30, 41, 59, 0.5);
          border: 1px solid rgba(148, 163, 184, 0.3);
          border-radius: 8px;
          color: white;
          font-size: 1rem;
          transition: all 0.3s ease;
          box-sizing: border-box;
        }
        
        .form-input:focus {
          outline: none;
          border-color: #8b5cf6;
          box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        
        .form-input::placeholder {
          color: #94a3b8;
        }
        
        .error-message {
          color: #ef4444;
          font-size: 0.875rem;
          margin-top: 0.25rem;
          min-height: 1.25rem;
        }
        
        .strength-container {
          margin-top: 0.5rem;
        }
        
        .strength-meter {
          height: 4px;
          background: rgba(148, 163, 184, 0.3);
          border-radius: 2px;
          overflow: hidden;
          position: relative;
        }
        
        .strength-meter::after {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          height: 100%;
          width: 0;
          transition: all 0.3s ease;
          border-radius: 2px;
        }
        
        .strength-meter.weak::after {
          width: 33%;
          background: #ef4444;
        }
        
        .strength-meter.medium::after {
          width: 66%;
          background: #f59e0b;
        }
        
        .strength-meter.strong::after {
          width: 100%;
          background: #10b981;
        }
        
        .strength-text {
          font-size: 0.75rem;
          margin-top: 0.25rem;
          font-weight: 500;
        }
        
        .strength-meter.weak + .strength-text {
          color: #ef4444;
        }
        
        .strength-meter.medium + .strength-text {
          color: #f59e0b;
        }
        
        .strength-meter.strong + .strength-text {
          color: #10b981;
        }
        
        .submit-button {
          width: 100%;
          padding: 0.75rem 1rem;
          background: linear-gradient(135deg, #8b5cf6, #ec4899);
          border: none;
          border-radius: 8px;
          color: white;
          font-weight: 600;
          font-size: 1rem;
          cursor: pointer;
          transition: all 0.3s ease;
          margin-top: 1rem;
        }
        
        .submit-button:hover {
          transform: translateY(-2px);
          box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.4);
        }
        
        .submit-button:active {
          transform: translateY(0);
        }
      </style>
      
      <div class="form-container">
        <h2 class="form-title">Welcome Back</h2>
        
        <form id="login-form">
          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input 
              type="email" 
              id="email" 
              class="form-input" 
              placeholder="Enter your email"
              required
            />
            <div id="email-error" class="error-message"></div>
          </div>
          
          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input 
              type="password" 
              id="password" 
              class="form-input" 
              placeholder="Enter your password"
              required
            />
            <div class="strength-container">
              <div id="strength-meter" class="strength-meter"></div>
              <div id="strength-text" class="strength-text"></div>
            </div>
            <div id="password-error" class="error-message"></div>
          </div>
          
          <button type="submit" class="submit-button">
            Sign In
          </button>
        </form>
      </div>
    `;
  }
}

customElements.define('custom-login-form', CustomLoginForm);
