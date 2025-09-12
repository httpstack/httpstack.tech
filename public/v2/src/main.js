document.addEventListener('DOMContentLoaded', () => {
    const header = document.getElementById('header');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    // Sticky Header Shadow
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                header.classList.add('shadow-md');
            } else {
                header.classList.remove('shadow-md');
            }
        });
    }

    // Mobile Menu Toggle
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        document.querySelectorAll('.nav-link-mobile').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
    }

    // Theme Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const themeIconLight = document.getElementById('theme-icon-light');
    const themeIconDark = document.getElementById('theme-icon-dark');
    const html = document.documentElement;

    const applyTheme = (theme) => {
        if (theme === 'dark') {
            html.classList.add('dark');
            if (themeIconDark) themeIconDark.classList.add('hidden');
            if (themeIconLight) themeIconLight.classList.remove('hidden');
        } else {
            html.classList.remove('dark');
            if (themeIconDark) themeIconDark.classList.remove('hidden');
            if (themeIconLight) themeIconLight.classList.add('hidden');
        }
    };

    // Initial theme apply is in <head> via a script tag to prevent FOUC
    // This just syncs the icon state
    const currentTheme = localStorage.getItem('theme') || 'light';
    applyTheme(currentTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const newTheme = html.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem('theme', newTheme);
            applyTheme(newTheme);
        });
    }


    // Slideshow for index.html
    const slideshowContainer = document.getElementById('slideshow-container');
    if (slideshowContainer) {
        const slides = document.querySelectorAll('.slide');
        const dotsContainer = document.getElementById('slideshow-dots');
        let currentSlide = 0;
        let slideInterval;

        if (slides.length > 0) {
            slides.forEach((_, index) => {
                const dot = document.createElement('button');
                dot.classList.add('dot', 'w-3', 'h-3', 'rounded-full', 'bg-white/50', 'transition-all');
                dot.addEventListener('click', () => {
                    setSlide(index);
                    resetInterval();
                });
                dotsContainer.appendChild(dot);
            });

            const dots = document.querySelectorAll('.dot');

            const setSlide = (index) => {
                slides.forEach(slide => slide.style.opacity = '0');
                dots.forEach(dot => dot.classList.remove('bg-white'));

                slides[index].style.opacity = '1';
                dots[index].classList.add('bg-white');
                currentSlide = index;
            };

            const nextSlide = () => {
                const newIndex = (currentSlide + 1) % slides.length;
                setSlide(newIndex);
            };

            const resetInterval = () => {
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000);
            };

            setSlide(0);
            resetInterval();
        }
    }
});