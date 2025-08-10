$(document).ready(function () {
    const $htmlEditor = $('#htmlEditor');
    const $cssEditor = $('#cssEditor');
    const $jsEditor = $('#jsEditor');
    const $previewFrame = $('#previewFrame');

    const $layoutDefaultBtn = $('#layoutDefault');
    const $layoutEditorsBtn = $('#layoutEditors');
    const $layoutStacksPreviewBtn = $('#layoutStacksPreview');
    const $mainContentRows = $('#mainContentRows');

    const $topRowContent = $('#top-row-content');
    const $bottomRowContent = $('#bottom-row-content');
    const $toggleTopRowBtn = $('#toggleTopRow');
    const $toggleBottomRowBtn = $('#toggleBottomRow');

    const $exportBtn = $('#exportBtn');
    const $saveBtn = $('#saveBtn');
    const $aiPrompt = $('#aiPrompt');
    const $generateBtn = $('#generateBtn');
    const $stackButtonsContainer = $('#stackButtons');

    // --- Live Preview Functionality ---
    function updatePreview() {
        const htmlContent = $htmlEditor.val();
        const cssContent = $cssEditor.val();
        const jsContent = $jsEditor.val();

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
        // jQuery's .attr() can set srcdoc, but direct property assignment is also common and fine
        $previewFrame[0].srcdoc = previewDoc; // Use [0] to get the native DOM element for srcdoc
    }

    // Update preview on input changes
    $htmlEditor.on('input', updatePreview);
    $cssEditor.on('input', updatePreview);
    $jsEditor.on('input', updatePreview);

    // Initial preview load
    updatePreview();

    // --- Layout Switching Functionality ---
    function activateLayoutButton(button) {
        $('.layout-btn').removeClass('active');
        $(button).addClass('active');
    }

    $layoutDefaultBtn.on('click', function () {
        activateLayoutButton(this); // 'this' inside jQuery event handler is the native DOM element
        $mainContentRows.css('flex-direction', 'column'); // Stack rows
        $topRowContent.css('display', 'flex'); // Ensure top row is visible
        $bottomRowContent.css('display', 'flex'); // Ensure bottom row is visible
        $topRowContent.removeClass('collapsed');
        $bottomRowContent.removeClass('collapsed');
        $topRowContent.css('flex', '1'); // Reset flex
        $bottomRowContent.css('flex', '1'); // Reset flex
    });

    $layoutEditorsBtn.on('click', function () {
        activateLayoutButton(this);
        $mainContentRows.css('flex-direction', 'column'); // Still columns, but top one hidden
        $topRowContent.css('display', 'none'); // Hide the top row (stacks/preview)
        $bottomRowContent.css('display', 'flex'); // Show editors
        $bottomRowContent.removeClass('collapsed');
        $bottomRowContent.css('flex', '1'); // Let editors take full space
    });

    $layoutStacksPreviewBtn.on('click', function () {
        activateLayoutButton(this);
        $mainContentRows.css('flex-direction', 'column'); // Still columns, but bottom one hidden
        $topRowContent.css('display', 'flex'); // Show stacks/preview
        $bottomRowContent.css('display', 'none'); // Hide editors
        $topRowContent.removeClass('collapsed');
        $topRowContent.css('flex', '1'); // Let stacks/preview take full space
    });

    // --- Row Collapsing Functionality ---
    $toggleTopRowBtn.on('click', function () {
        $topRowContent.toggleClass('collapsed');
    });

    $toggleBottomRowBtn.on('click', function () {
        $bottomRowContent.toggleClass('collapsed');
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
            $htmlEditor.val(stack.html.trim());
            $cssEditor.val(stack.css.trim());
            $jsEditor.val(stack.js.trim());
            updatePreview(); // Update preview after loading
        } else {
            console.warn(`Stack with ID "${stackId}" not found.`);
        }
    }

    // Attach click listeners to stack buttons using event delegation
    $stackButtonsContainer.on('click', '.stack-item', function () {
        const stackId = $(this).data('stackId'); // Use .data() for data-attributes
        loadStack(stackId);
    });

    // Load a default stack on initial page load
    loadStack('hero-section');


    // --- Export Functionality (Placeholder) ---
    $exportBtn.on('click', function () {
        const htmlBlob = new Blob([$htmlEditor.val()], { type: 'text/html' });
        const cssBlob = new Blob([$cssEditor.val()], { type: 'text/css' });
        const jsBlob = new Blob([$jsEditor.val()], { type: 'text/javascript' });

        const downloadFile = (blob, filename) => {
            const url = URL.createObjectURL(blob);
            const $a = $('<a>'); // Create an <a> element with jQuery
            $a.attr('href', url);
            $a.attr('download', filename);
            $('body').append($a); // Append to body with jQuery
            $a[0].click(); // Get native element to trigger click
            $a.remove(); // Remove with jQuery
            URL.revokeObjectURL(url);
        };

        const baseFilename = 'stack_export';
        downloadFile(htmlBlob, `${baseFilename}.html`);
        downloadFile(cssBlob, `${baseFilename}.css`);
        downloadFile(jsBlob, `${baseFilename}.js`);

        alert('Export initiated! Check your downloads.');
    });

    // --- Save Functionality (Placeholder) ---
    $saveBtn.on('click', function () {
        alert('Save functionality coming soon! (Will save to your httpstack database)');
        // Here you would send the $htmlEditor.val(), $cssEditor.val(), $jsEditor.val()
        // along with user authentication details to your backend API.
    });

    // --- AI Generation (Placeholder) ---
    $generateBtn.on('click', function () {
        const promptText = $aiPrompt.val().trim(); // Use .val()
        if (promptText) {
            alert(`AI generation requested for: "${promptText}"\n(Integration with Gemini API to be implemented!)`);
            // Here you would make an API call to Gemini with the promptText
            // and then update the editor values with the AI's response.
        } else {
            alert('Please enter a prompt for AI generation.');
        }
    });
});