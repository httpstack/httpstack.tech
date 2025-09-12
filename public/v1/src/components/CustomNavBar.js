
class CustomNavBar extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });
    this.isDark = true;
  }

  connectedCallback() {
    this.render();
    this.setupEventListeners();
  }

  setupEventListeners() {
    const themeToggle = this.shadowRoot.querySelector('#theme-toggle');
    themeToggle.addEventListener('click', () => this.toggleTheme());
  }

  toggleTheme() {
    this.isDark = !this.isDark;
    document.documentElement.classList.toggle('dark', this.isDark);
    this.updateThemeIcon();
  }

  updateThemeIcon() {
    const icon = this.shadowRoot.querySelector('#theme-icon');
    icon.innerHTML = this.isDark ? 
      '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>' :
      '<circle cx="12" cy="12" r="5"></circle><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"></path>';
  }

  render() {
    this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          width: 100%;
        }
        
        .navbar {
          background: rgba(15, 23, 42, 0.9);
          backdrop-filter: blur(10px);
          border-bottom: 1px solid rgba(148, 163, 184, 0.1);
          padding: 1rem 0;
          position: sticky;
          top: 0;
          z-index: 50;
        }
        
        .nav-container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 1rem;
          display: flex;
          justify-content: space-between;
          align-items: center;
        }
        
        .logo-section {
          display: flex;
          align-items: center;
          gap: 0.75rem;
        }
        
        .logo-placeholder {
          width: 40px;
          height: 40px;
          background: linear-gradient(135deg, #8b5cf6, #ec4899);
          border-radius: 8px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-weight: bold;
          font-size: 1.25rem;
        }
        
        .brand-text {
          color: white;
          font-size: 1.5rem;
          font-weight: 700;
          background: linear-gradient(135deg, #8b5cf6, #ec4899);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          background-clip: text;
        }
        
        .nav-actions {
          display: flex;
          align-items: center;
          gap: 1rem;
        }
        
        .theme-toggle {
          background: rgba(148, 163, 184, 0.1);
          border: 1px solid rgba(148, 163, 184, 0.2);
          border-radius: 8px;
          padding: 0.5rem;
          cursor: pointer;
          transition: all 0.3s ease;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        
        .theme-toggle:hover {
          background: rgba(148, 163, 184, 0.2);
          transform: translateY(-1px);
        }
        
        .theme-icon {
          width: 20px;
          height: 20px;
          stroke: #e2e8f0;
          fill: none;
          stroke-width: 2;
          stroke-linecap: round;
          stroke-linejoin: round;
        }
        
        .nav-menu {
          display: flex;
          gap: 2rem;
          list-style: none;
          margin: 0;
          padding: 0;
        }
        
        .nav-link {
          color: #e2e8f0;
          text-decoration: none;
          font-weight: 500;
          transition: color 0.3s ease;
          position: relative;
        }
        
        .nav-link:hover {
          color: #8b5cf6;
        }
        
        .nav-link::after {
          content: '';
          position: absolute;
          bottom: -4px;
          left: 0;
          width: 0;
          height: 2px;
          background: linear-gradient(135deg, #8b5cf6, #ec4899);
          transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
          width: 100%;
        }
        
        @media (max-width: 768px) {
          .nav-menu {
            display: none;
          }
          
          .brand-text {
            font-size: 1.25rem;
          }
        }
      </style>
      
      <nav class="navbar">
        <div class="nav-container">
          <div class="logo-section">
            <div class="logo-placeholder">W</div>
            <span class="brand-text">WebComponents</span>
          </div>
          
          <ul class="nav-menu">
            <li><a href="#" class="nav-link">Home</a></li>
            <li><a href="#" class="nav-link">About</a></li>
            <li><a href="#" class="nav-link">Contact</a></li>
          </ul>
          
          <div class="nav-actions">
            <button id="theme-toggle" class="theme-toggle" title="Toggle theme">
              <svg id="theme-icon" class="theme-icon" viewBox="0 0 24 24">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
              </svg>
            </button>
          </div>
        </div>
      </nav>
    `;
  }
}

customElements.define('custom-nav-bar', CustomNavBar);
