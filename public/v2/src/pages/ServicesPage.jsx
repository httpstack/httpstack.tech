import React from 'react';
import PageWrapper from '@/components/PageWrapper';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { motion } from 'framer-motion';
import { Code, Brush, Server, HardDrive } from 'lucide-react';

const services = [
    {
        icon: <Code className="w-12 h-12 text-cyan-400" />,
        title: "Web Programming",
        description: "Building dynamic, responsive, and high-performance websites and web applications using modern technologies."
    },
    {
        icon: <Brush className="w-12 h-12 text-yellow-400" />,
        title: "Graphic Development",
        description: "Creating stunning visuals, UI/UX designs, and brand identities that captivate and engage your audience."
    },
    {
        icon: <Server className="w-12 h-12 text-green-400" />,
        title: "Server/Network Development",
        description: "Designing and administering robust, scalable, and secure server infrastructures and network solutions."
    },
    {
        icon: <HardDrive className="w-12 h-12 text-red-400" />,
        title: "Computer/Server Maintenance",
        description: "Providing expert installation, maintenance, and repair services for computers and servers to ensure peak performance."
    }
];

const ServicesPage = () => {
    return (
        <PageWrapper title="Services" description="Discover the professional services I offer.">
            <div className="container mx-auto px-6 py-20 md:py-28">
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5 }}
                    className="text-center mb-16"
                >
                    <h1 className="text-4xl md:text-5xl font-bold mb-4">What I <span className="text-cyan-400">Offer</span></h1>
                    <p className="text-lg text-gray-300 max-w-2xl mx-auto">From code to cloud, I provide a wide range of services to bring your ideas to life.</p>
                </motion.div>

                <div className="grid md:grid-cols-2 gap-8">
                    {services.map((service, index) => (
                        <motion.div
                            key={index}
                            initial={{ opacity: 0, y: 50 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, amount: 0.3 }}
                            transition={{ duration: 0.5, delay: index * 0.1 }}
                        >
                            <Card className="bg-indigo-900/40 border-purple-500/30 h-full hover:border-cyan-400/50 hover:bg-indigo-900/60 transition-all duration-300 transform hover:-translate-y-2">
                                <CardHeader className="flex flex-row items-center gap-6">
                                    <div className="p-4 bg-gray-800/50 rounded-full">
                                        {service.icon}
                                    </div>
                                    <CardTitle className="text-2xl font-semibold text-white">{service.title}</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-gray-400 ml-20">{service.description}</p>
                                </CardContent>
                            </Card>
                        </motion.div>
                    ))}
                </div>
            </div>
        </PageWrapper>
    );
};

export default ServicesPage;