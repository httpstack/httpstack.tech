class AppHeader extends HTMLElement {
    constructor() {
        super();
        // Attach a shadow DOM to the custom element
        this.attachShadow({ mode: 'open' });
        // The shadow DOM acts as an encapsulated subtree for the element.
    }

    connectedCallback() {
        // This callback is fired when the element is inserted into the DOM.
        this.render();
    }

    render() {
        this.shadowRoot.innerHTML = `
            <style>
                /* Internal styles for the component, scoped to its Shadow DOM */
                :host {
                    display: block;
                    width: 100%;
                    background-color: #282c34;
                    color: #61dafb;
                    padding: 10px 20px;
                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                    box-sizing: border-box;
                }

                .header-content {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    max-width: 1200px;
                    margin: 0 auto;
                }

                .logo {
                    font-size: 24px;
                    font-weight: bold;
                    color: #fff;
                    text-decoration: none;
                }

                nav ul {
                    list-style: none;
                    margin: 0;
                    padding: 0;
                    display: flex;
                }

                nav ul li {
                    margin-left: 20px;
                }

                nav ul li a {
                    color: #61dafb;
                    text-decoration: none;
                    font-weight: bold;
                    transition: color 0.3s ease;
                }

                nav ul li a:hover {
                    color: #fff;
                }
            </style>
            <div class="header-content">
                <a href="#" class="logo">Stack Builder</a>
                <nav>
                    <ul>
                        <li><a href="#">My Stacks</a></li>
                        <li><a href="#">Explore</a></li>
                        <li><a href="#">Account</a></li>
                    </ul>
                </nav>
            </div>
        `;
    }

    disconnectedCallback() {
        // Fired when the element is removed from the DOM.
        // Good for cleanup like removing event listeners.
        console.log('AppHeader disconnected from DOM');
    }

    adoptedCallback() {
        // Fired when the element is moved to a new document.
        console.log('AppHeader adopted to new document');
    }

    attributeChangedCallback(name, oldValue, newValue) {
        // Fired when one of the element's attributes is added, removed, or changed.
        // We don't have observedAttributes yet, but this is where it would go.
        console.log(`Attribute ${name} changed from ${oldValue} to ${newValue}`);
    }

    // You can define static get observedAttributes() here if you want to react to attribute changes.
    // static get observedAttributes() { return ['some-attribute']; }
}

// Define the custom element
customElements.define('app-header', AppHeader);