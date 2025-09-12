import React from 'react';
import { Github, Linkedin, Twitter } from 'lucide-react';

const Footer = () => {
    const year = new Date().getFullYear();

    return (
        <footer className="bg-gray-900/50 text-gray-400 py-8">
            <div className="container mx-auto px-6 text-center">
                <div className="flex justify-center space-x-6 mb-4">
                    <a href="#" className="hover:text-cyan-400 transition-colors"><Twitter /></a>
                    <a href="#" className="hover:text-cyan-400 transition-colors"><Linkedin /></a>
                    <a href="#" className="hover:text-cyan-400 transition-colors"><Github /></a>
                </div>
                <p>&copy; {year} httpstack. All Rights Reserved.</p>
                <p className="text-sm mt-2">Connecting developers, empowering businesses.</p>
            </div>
        </footer>
    );
};

export default Footer;