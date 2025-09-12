import React from 'react';
import { motion } from 'framer-motion';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Users, Zap, Shield } from 'lucide-react';

const features = [
    {
        icon: <Users className="w-10 h-10 text-cyan-400" />,
        title: "Community Driven",
        description: "Join a thriving community of developers and specifiers. Share your stack, discover new solutions, and collaborate on the future of development."
    },
    {
        icon: <Zap className="w-10 h-10 text-yellow-400" />,
        title: "Empowering Innovation",
        description: "httpstack provides the tools and platform to turn your architectural ideas into industry standards. Unleash your potential."
    },
    {
        icon: <Shield className="w-10 h-10 text-green-400" />,
        title: "Built on Standards",
        description: "Create, share, and discover verifiable stack specifications. Bring clarity and consistency to your projects and teams."
    }
];

const AboutSection = () => {
    return (
        <section id="about" className="py-20 md:py-32 bg-gray-900/50">
            <div className="container mx-auto px-6">
                <motion.div
                    initial={{ opacity: 0, y: 50 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true, amount: 0.3 }}
                    transition={{ duration: 0.6 }}
                >
                    <h2 className="text-4xl md:text-5xl font-bold text-center mb-4">Why <span className="bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-purple-500">httpstack?</span></h2>
                    <p className="text-lg text-gray-300 text-center max-w-3xl mx-auto mb-16">
                        We are dedicated to creating a new standard for defining and sharing software architecture. Here's what makes our platform unique.
                    </p>
                </motion.div>

                <div className="grid md:grid-cols-3 gap-8">
                    {features.map((feature, index) => (
                         <motion.div
                            key={index}
                            initial={{ opacity: 0, y: 50 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, amount: 0.5 }}
                            transition={{ duration: 0.5, delay: index * 0.2 }}
                        >
                            <Card className="bg-indigo-900/40 border-purple-500/30 h-full text-center hover:border-cyan-400/50 hover:bg-indigo-900/60 transition-all duration-300 transform hover:-translate-y-2">
                                <CardHeader className="flex flex-col items-center">
                                    <div className="p-4 bg-gray-800/50 rounded-full mb-4">
                                        {feature.icon}
                                    </div>
                                    <CardTitle className="text-2xl font-semibold text-white">{feature.title}</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-gray-400">{feature.description}</p>
                                </CardContent>
                            </Card>
                        </motion.div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default AboutSection;