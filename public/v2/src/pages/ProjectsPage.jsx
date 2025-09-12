import React from 'react';
import PageWrapper from '@/components/PageWrapper';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { motion } from 'framer-motion';
import { Button } from '@/components/ui/button';
import { Github, ExternalLink } from 'lucide-react';
import { useToast } from "@/components/ui/use-toast";

const projects = [
    {
        title: "Project Alpha",
        description: "A cutting-edge web application built with the latest technologies to solve a complex business problem.",
        imageText: "Abstract visualization of data streams and network connections in neon blue and purple",
        tags: ["React", "Node.js", "GraphQL"],
    },
    {
        title: "Project Beta",
        description: "An innovative mobile app designed for seamless user experience and powerful functionality on the go.",
        imageText: "Sleek and modern mobile app interface mockup on a smartphone screen",
        tags: ["React Native", "Firebase", "AI"],
    },
    {
        title: "Project Gamma",
        description: "A robust server infrastructure and network administration project ensuring 99.99% uptime and security.",
        imageText: "Server racks in a data center with glowing LED lights and network cables",
        tags: ["DevOps", "AWS", "Security"],
    },
];

const ProjectsPage = () => {
    const { toast } = useToast();

    const handleNotImplemented = () => {
        toast({
            title: "🚧 Feature In Progress",
            description: "This feature isn't implemented yet—but don't worry! You can request it in your next prompt! 🚀",
        });
    };

    return (
        <PageWrapper title="Projects" description="A showcase of my latest work and projects.">
            <div className="container mx-auto px-6 py-20 md:py-28">
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5 }}
                    className="text-center mb-16"
                >
                    <h1 className="text-4xl md:text-5xl font-bold mb-4">My <span className="text-cyan-400">Projects</span></h1>
                    <p className="text-lg text-gray-300 max-w-2xl mx-auto">Here's a selection of some things I'm proud of. I'm always working on something new!</p>
                </motion.div>

                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {projects.map((project, index) => (
                        <motion.div
                            key={index}
                            initial={{ opacity: 0, y: 50 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, amount: 0.3 }}
                            transition={{ duration: 0.5, delay: index * 0.1 }}
                        >
                            <Card className="bg-indigo-900/40 border-purple-500/30 h-full flex flex-col overflow-hidden group hover:border-cyan-400/50 transition-all duration-300 transform hover:-translate-y-2">
                                <div className="relative h-48 bg-gray-800">
                                    <img  className="w-full h-full object-cover" alt={project.title} src="https://images.unsplash.com/photo-1572177812156-58036aae439c" />
                                    <div className="absolute inset-0 bg-black/40"></div>
                                </div>
                                <CardHeader>
                                    <CardTitle className="text-2xl font-semibold text-white">{project.title}</CardTitle>
                                    <div className="flex flex-wrap gap-2 mt-2">
                                        {project.tags.map(tag => (
                                            <span key={tag} className="text-xs font-semibold bg-cyan-400/20 text-cyan-300 px-2 py-1 rounded-full">{tag}</span>
                                        ))}
                                    </div>
                                </CardHeader>
                                <CardContent className="flex-grow">
                                    <CardDescription className="text-gray-400">{project.description}</CardDescription>
                                </CardContent>
                                <div className="p-6 pt-0 flex justify-end gap-2">
                                    <Button variant="ghost" size="icon" onClick={handleNotImplemented}><Github className="h-5 w-5" /></Button>
                                    <Button variant="ghost" size="icon" onClick={handleNotImplemented}><ExternalLink className="h-5 w-5" /></Button>
                                </div>
                            </Card>
                        </motion.div>
                    ))}
                </div>
            </div>
        </PageWrapper>
    );
};

export default ProjectsPage;