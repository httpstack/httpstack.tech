document.addEventListener('DOMContentLoaded', () => {
    const htmlEditor = document.getElementById('htmlEditor');
    const cssEditor = document.getElementById('cssEditor');
    const jsEditor = document.getElementById('jsEditor');
    const previewFrame = document.getElementById('previewFrame');

    const layoutDefaultBtn = document.getElementById('layoutDefault');
    const layoutEditorsBtn = document.getElementById('layoutEditors');
    const layoutStacksPreviewBtn = document.getElementById('layoutStacksPreview');
    const mainContentRows = document.getElementById('mainContentRows');

    const topRowContent = document.getElementById('top-row-content');
    const bottomRowContent = document.getElementById('bottom-row-content');
    const toggleTopRowBtn = document.getElementById('toggleTopRow');
    const toggleBottomRowBtn = document.getElementById('toggleBottomRow');

    const exportBtn = document.getElementById('exportBtn');
    const saveBtn = document.getElementById('saveBtn');
    const aiPrompt = document.getElementById('aiPrompt');
    const generateBtn = document.getElementById('generateBtn');
    const stackButtonsContainer = document.getElementById('stackButtons');

    // --- Live Preview Functionality ---
    function updatePreview() {
        const htmlContent = htmlEditor.value;
        const cssContent = cssEditor.value;
        const jsContent = jsEditor.value;

        // Create the full HTML document for the iframe
        const previewDoc = `
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Preview</title>
                <style>${cssContent}</style>
            </head>
            <body>
                ${htmlContent}
                <script>${jsContent}<\/script>
            </body>
            </html>
        `;
        previewFrame.srcdoc = previewDoc;
    }

    // Update preview on input changes
    htmlEditor.addEventListener('input', updatePreview);
    cssEditor.addEventListener('input', updatePreview);
    jsEditor.addEventListener('input', updatePreview);

    // Initial preview load
    updatePreview();

    // --- Layout Switching Functionality ---
    function activateLayoutButton(button) {
        document.querySelectorAll('.layout-btn').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
    }

    layoutDefaultBtn.addEventListener('click', () => {
        activateLayoutButton(layoutDefaultBtn);
        mainContentRows.style.flexDirection = 'column'; // Stack rows
        topRowContent.style.display = 'flex'; // Ensure top row is visible
        bottomRowContent.style.display = 'flex'; // Ensure bottom row is visible
        topRowContent.classList.remove('collapsed');
        bottomRowContent.classList.remove('collapsed');
        topRowContent.style.flex = '1'; // Reset flex
        bottomRowContent.style.flex = '1'; // Reset flex
    });

    layoutEditorsBtn.addEventListener('click', () => {
        activateLayoutButton(layoutEditorsBtn);
        mainContentRows.style.flexDirection = 'column'; // Still columns, but top one hidden
        topRowContent.style.display = 'none'; // Hide the top row (stacks/preview)
        bottomRowContent.style.display = 'flex'; // Show editors
        bottomRowContent.classList.remove('collapsed');
        bottomRowContent.style.flex = '1'; // Let editors take full space
    });

    layoutStacksPreviewBtn.addEventListener('click', () => {
        activateLayoutButton(layoutStacksPreviewBtn);
        mainContentRows.style.flexDirection = 'column'; // Still columns, but bottom one hidden
        topRowContent.style.display = 'flex'; // Show stacks/preview
        bottomRowContent.style.display = 'none'; // Hide editors
        topRowContent.classList.remove('collapsed');
        topRowContent.style.flex = '1'; // Let stacks/preview take full space
    });

    // --- Row Collapsing Functionality ---
    toggleTopRowBtn.addEventListener('click', () => {
        topRowContent.classList.toggle('collapsed');
    });

    toggleBottomRowBtn.addEventListener('click', () => {
        bottomRowContent.classList.toggle('collapsed');
    });

    // --- Placeholder Stack Data (Simulating Database) ---
    const stacks = {
        'hero-section': {
            html: `
<section class="hero">
    <h1>Welcome to Stack Builder!</h1>
    <p>Build, preview, and export web components with ease.</p>
    <button class="cta-button">Get Started</button>
</section>`,
            css: `
.hero {
    background-color: #007bff;
    color: white;
    padding: 50px 20px;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.hero h1 {
    font-size: 2.5em;
    margin-bottom: 15px;
}
.hero p {
    font-size: 1.2em;
    margin-bottom: 30px;
}
.cta-button {
    background-color: #28a745;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1.1em;
    transition: background-color 0.3s ease;
}
.cta-button:hover {
    background-color: #218838;
}`,
            js: `
document.querySelector('.cta-button').addEventListener('click', () => {
    alert('You clicked the Call to Action button!');
});`
        },
        'call-to-action': {
            html: `
<div class="cta-banner">
    <h2>Ready to build something amazing?</h2>
    <p>Sign up today and unleash your creativity.</p>
    <a href="#" class="signup-button">Sign Up Now</a>
</div>`,
            css: `
.cta-banner {
    background-color: #ffc107;
    color: #333;
    padding: 30px 20px;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.cta-banner h2 {
    font-size: 2em;
    margin-bottom: 10px;
}
.cta-banner p {
    font-size: 1.1em;
    margin-bottom: 20px;
}
.signup-button {
    background-color: #dc3545;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease;
}
.signup-button:hover {
    background-color: #c82333;
}`
            ,
            js: `
document.querySelector('.signup-button').addEventListener('click', (e) => {
    e.preventDefault();
    console.log('Sign Up button clicked!');
    // In a real app, this would redirect to a signup page or open a modal
});`
        }
    };

    // --- Load Stack Content into Editors ---
    function loadStack(stackId) {
        const stack = stacks[stackId];
        if (stack) {
            htmlEditor.value = stack.html.trim();
            cssEditor.value = stack.css.trim();
            jsEditor.value = stack.js.trim();
            updatePreview(); // Update preview after loading
        } else {
            console.warn(`Stack with ID "${stackId}" not found.`);
        }
    }

    // Attach click listeners to stack buttons
    stackButtonsContainer.addEventListener('click', (event) => {
        if (event.target.classList.contains('stack-item')) {
            const stackId = event.target.dataset.stackId;
            loadStack(stackId);
        }
    });

    // Load a default stack on initial page load
    loadStack('hero-section');


    // --- Export Functionality (Placeholder) ---
    exportBtn.addEventListener('click', () => {
        const htmlBlob = new Blob([htmlEditor.value], { type: 'text/html' });
        const cssBlob = new Blob([cssEditor.value], { type: 'text/css' });
        const jsBlob = new Blob([jsEditor.value], { type: 'text/javascript' });

        // For simplicity, we'll create download links directly.
        // In a real app, you'd use a library like JSZip to create a zip file
        // and offer it for download.

        const downloadFile = (blob, filename) => {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        };

        const baseFilename = 'stack_export';
        downloadFile(htmlBlob, `${baseFilename}.html`);
        downloadFile(cssBlob, `${baseFilename}.css`);
        downloadFile(jsBlob, `${baseFilename}.js`);

        alert('Export initiated! Check your downloads.');
    });

    // --- Save Functionality (Placeholder) ---
    saveBtn.addEventListener('click', () => {
        alert('Save functionality coming soon! (Will save to your httpstack database)');
        // Here you would send the htmlEditor.value, cssEditor.value, jsEditor.value
        // along with user authentication details to your backend API.
    });

    // --- AI Generation (Placeholder) ---
    generateBtn.addEventListener('click', () => {
        const promptText = aiPrompt.value.trim();
        if (promptText) {
            alert(`AI generation requested for: "${promptText}"\n(Integration with Gemini API to be implemented!)`);
            // Here you would make an API call to Gemini with the promptText
            // and then update the editor values with the AI's response.
        } else {
            alert('Please enter a prompt for AI generation.');
        }
    });
});