import React from 'react';
import PageWrapper from '@/components/PageWrapper';
import HeroSection from '@/components/HeroSection';
import AboutSection from '@/components/AboutSection';
import ContactSection from '@/components/ContactSection';

const HomePage = () => {
    return (
        <PageWrapper title="Home" description="httpstack: Connecting developers, empowering businesses. Your platform for community-driven development and commercial success.">
            <HeroSection />
            <AboutSection />
            <ContactSection />
        </PageWrapper>
    );
};

export default HomePage;