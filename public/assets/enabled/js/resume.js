$(function () {
    // --- Resume Download Form Logic ---
    $('.download-form').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission
        const selectedFormat = $('#resumeFormat').val();
        // In a real application, you would have backend routes to serve these files.
        // For now, we'll simulate the download or provide a direct link if possible.
        let downloadUrl = '';

        switch (selectedFormat) {
            case 'pdf':
                downloadUrl = 'Chris-McIntosh.pdf'; // Assuming PDF is in the same directory or accessible path
                break;
            case 'docx':
                // Placeholder: You'd need a .docx version of your resume on your server
                // downloadUrl = '/path/to/Chris-McIntosh.docx';
                alert('DOCX format download is not yet available. Please try PDF.');
                return; // Stop execution if format not supported
            case 'odt':
                // Placeholder: You'd need an .odt version
                // downloadUrl = '/path/to/Chris-McIntosh.odt';
                alert('ODT format download is not yet available. Please try PDF.');
                return;
            case 'txt':
                // Placeholder: You'd need a .txt version
                // downloadUrl = '/path/to/Chris-McIntosh.txt';
                alert('Plain Text format download is not yet available. Please try PDF.');
                return;
            default:
                alert('Invalid format selected.');
                return;
        }

        if (downloadUrl) {
            // Create a temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = `Chris-McIntosh-Resume.${selectedFormat}`; // Suggest filename
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            alert(`Downloading resume in ${selectedFormat.toUpperCase()} format...`);
        }
    });

    // --- Resume Lightbox Logic ---
    const $resumeLightbox = $('#resumeLightbox');
    const $closeButton = $resumeLightbox.find('.close-button');
    const $resumePdfViewer = $('#resumePdfViewer');
    const $viewResumeLightbox = $('#viewResumeLightbox');

    $viewResumeLightbox.on('click', function () {
        // Set the PDF source for the iframe
        $resumePdfViewer.attr('src', 'Chris-McIntosh.pdf'); // Assuming PDF is in the same directory
        $resumeLightbox.addClass('open'); // Open the lightbox
        $('body').addClass('no-scroll'); // Prevent body scrolling
    });

    $closeButton.on('click', function () {
        $resumeLightbox.removeClass('open'); // Close the lightbox
        $resumePdfViewer.attr('src', ''); // Clear iframe src to stop PDF loading
        $('body').removeClass('no-scroll'); // Re-enable body scrolling
    });

    // Close lightbox if clicking outside the content
    $(window).on('click', function (event) {
        if ($(event.target).is($resumeLightbox)) {
            $resumeLightbox.removeClass('open');
            $resumePdfViewer.attr('src', '');
            $('body').removeClass('no-scroll');
        }
    });

    // Close lightbox with Escape key
    $(document).on('keydown', function (event) {
        if (event.key === 'Escape' && $resumeLightbox.hasClass('open')) {
            $resumeLightbox.removeClass('open');
            $resumePdfViewer.attr('src', '');
            $('body').removeClass('no-scroll');
        }
    });
});