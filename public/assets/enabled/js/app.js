$(function () {
    // Hamburger menu functionality
    const $hamburger = $('.hamburger-menu');
    const $nav = $('.header-nav');

    $hamburger.on('click', function () {
        $nav.toggleClass('nav-open'); // Removed the dot from .nav-open here
        // Toggle hamburger icon for visual feedback (e.g., X icon)
        $(this).find('i').toggleClass('fa-bars fa-times');
    });

    // Close navigation when a link is clicked (for mobile)
    $nav.find('.nav-link').on('click', function () {
        if ($nav.hasClass('nav-open')) {
            $nav.removeClass('nav-open');
            $hamburger.find('i').removeClass('fa-times').addClass('fa-bars');
        }
    });


    // Get the lightbox element
    const lightbox = document.getElementById('lightbox');

    // Function to open the lightbox
    function openLightbox() {
        lightbox.style.display = 'block';
    }

    // Function to close the lightbox
    function closeLightbox() {
        lightbox.style.display = 'none';
    }

    // Close the lightbox if the user presses the 'Escape' key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && lightbox.style.display === 'block') {
            closeLightbox();
        }
    });

    // Close navigation if clicking outside on mobile
    $(document).on('click', function (event) {
        if (!$nav.is(event.target) && $nav.has(event.target).length === 0 &&
            !$hamburger.is(event.target) && $hamburger.has(event.target).length === 0 &&
            $nav.hasClass('nav-open')) {
            $nav.removeClass('nav-open');
            $hamburger.find('i').removeClass('fa-times').addClass('fa-bars');
        }
    });


    // --- Theme Toggle Logic (now using jQuery) ---
    const $themeToggle = $('.theme-toggle');

    // Function to set the theme
    function setTheme(theme) {
        $('html').attr('data-theme', theme);
        localStorage.setItem('theme', theme); // Save user preference
    }

    // Check for saved theme preference on load
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        setTheme(savedTheme);
    } else {
        // Default to light theme if no preference is saved
        setTheme('light');
    }

    // Toggle theme on button click
    $themeToggle.on('click', function () {
        const currentTheme = $('html').attr('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });
});