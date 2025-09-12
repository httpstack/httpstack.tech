import React from 'react';
import { Routes, Route, useLocation } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import Header from '@/components/Header';
import ScrollToTopButton from '@/components/ScrollToTopButton';
import { Toaster } from "@/components/ui/toaster";
import HomePage from '@/pages/HomePage';
import ProjectsPage from '@/pages/ProjectsPage';
import ServicesPage from '@/pages/ServicesPage';
import ResumePage from '@/pages/ResumePage';
import LoginPage from '@/pages/LoginPage';
import RegisterPage from '@/pages/RegisterPage';
import Footer from '@/components/Footer';
import { Helmet } from 'react-helmet';
import StackSpecPage from '@/pages/StackSpecPage'; // Import the new page

function App() {
    const location = useLocation();

    return (
        <>
            <Helmet>
                <title>httpstack - Connecting Developers, Empowering Businesses</title>
                <meta name="description" content="httpstack: Your platform for community-driven development and commercial success. Connect, collaborate, and build." />
                <meta property="og:title" content="httpstack - Connecting Developers, Empowering Businesses" />
                <meta property="og:description" content="httpstack: Your platform for community-driven development and commercial success. Connect, collaborate, and build." />
            </Helmet>
            <div className="bg-gradient-to-br from-indigo-900 via-purple-900 to-gray-900 text-white min-h-screen font-sans flex flex-col">
                <Header />
                <main className="flex-grow">
                    <AnimatePresence mode="wait">
                        <Routes location={location} key={location.pathname}>
                            <Route path="/" element={<HomePage />} />
                            <Route path="/projects" element={<ProjectsPage />} />
                            <Route path="/services" element={<ServicesPage />} />
                            <Route path="/resume" element={<ResumePage />} />
                            <Route path="/stack-spec" element={<StackSpecPage />} /> {/* New Route */}
                            <Route path="/login" element={<LoginPage />} />
                            <Route path="/register" element={<RegisterPage />} />
                        </Routes>
                    </AnimatePresence>
                </main>
                <Footer />
                <ScrollToTopButton />
                <Toaster />
            </div>
        </>
    );
}

export default App;