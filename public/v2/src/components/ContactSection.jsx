
import React from 'react';
import { motion } from 'framer-motion';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { useToast } from "@/components/ui/use-toast";
import { Send } from 'lucide-react';

const ContactSection = () => {
    const { toast } = useToast();

    const handleSubmit = (e) => {
        e.preventDefault();
        toast({
            title: "🚧 Message Not Sent",
            description: "This feature isn't implemented yet—but don't worry! You can request it in your next prompt! 🚀",
        });
    };

    return (
        <section id="contact" className="py-20 md:py-32 bg-gradient-to-t from-indigo-900 via-purple-900 to-gray-900">
            <div className="container mx-auto px-6">
                <motion.div
                    initial={{ opacity: 0, y: 50 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, amount: 0.3 }}
                    transition={{ duration: 0.6 }}
                >
                    <Card className="max-w-2xl mx-auto bg-black/40 backdrop-blur-md border-purple-500/30">
                        <CardHeader className="text-center">
                            <CardTitle className="text-4xl font-bold text-white">Get In Touch</CardTitle>
                            <CardDescription className="text-gray-300 mt-2">
                                Have questions or want to collaborate? Drop us a line!
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="space-y-2">
                                    <input
                                        type="text"
                                        placeholder="Your Name"
                                        className="w-full p-3 bg-gray-800/50 border border-gray-700 rounded-md text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition"
                                        required
                                    />
                                </div>
                                <div className="space-y-2">
                                    <input
                                        type="email"
                                        placeholder="Your Email"
                                        className="w-full p-3 bg-gray-800/50 border border-gray-700 rounded-md text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition"
                                        required
                                    />
                                </div>
                                <div className="space-y-2">
                                    <textarea
                                        placeholder="Your Message"
                                        rows="5"
                                        className="w-full p-3 bg-gray-800/50 border border-gray-700 rounded-md text-white focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition"
                                        required
                                    ></textarea>
                                </div>
                                <div className="text-center">
                                    <Button size="lg" type="submit" className="bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-600 hover:to-purple-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transform hover:scale-105 transition-transform">
                                        <Send className="mr-2 h-5 w-5" />
                                        Send Message
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </motion.div>
            </div>
        </section>
    );
};

export default ContactSection;
