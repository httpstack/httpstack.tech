import React from 'react';
import PageWrapper from '@/components/PageWrapper';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { motion } from 'framer-motion';
import { useToast } from "@/components/ui/use-toast";
import { LogIn } from 'lucide-react';
import { Link } from 'react-router-dom';

const LoginPage = () => {
    const { toast } = useToast();

    const handleSubmit = (e) => {
        e.preventDefault();
        toast({
            title: "🚧 Login Failed",
            description: "This feature isn't implemented yet—but don't worry! You can request it in your next prompt! 🚀",
        });
    };

    return (
        <PageWrapper title="Login" description="Access your account.">
            <div className="container mx-auto px-6 py-20 md:py-28 flex items-center justify-center min-h-[70vh]">
                <motion.div
                    initial={{ opacity: 0, y: 50 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5 }}
                    className="w-full max-w-md"
                >
                    <Card className="bg-black/40 backdrop-blur-md border-purple-500/30">
                        <CardHeader className="text-center">
                            <CardTitle className="text-4xl font-bold text-white">Welcome Back!</CardTitle>
                            <CardDescription className="text-gray-300 mt-2">
                                Sign in to continue.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="space-y-2">
                                    <input
                                        type="email"
                                        placeholder="Email"
                                        className="w-full p-3 bg-gray-800/50 border border-gray-700 rounded-md text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition"
                                        required
                                    />
                                </div>
                                <div className="space-y-2">
                                    <input
                                        type="password"
                                        placeholder="Password"
                                        className="w-full p-3 bg-gray-800/50 border border-gray-700 rounded-md text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition"
                                        required
                                    />
                                </div>
                                <Button size="lg" type="submit" className="w-full bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-600 hover:to-purple-700 text-white font-bold py-3 rounded-full shadow-lg transform hover:scale-105 transition-transform">
                                    <LogIn className="mr-2 h-5 w-5" />
                                    Login
                                </Button>
                            </form>
                            <p className="text-center text-gray-400 mt-6">
                                Don't have an account? <Link to="/register" className="text-cyan-400 hover:underline">Register here</Link>
                            </p>
                        </CardContent>
                    </Card>
                </motion.div>
            </div>
        </PageWrapper>
    );
};

export default LoginPage;