import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { ChevronLeft, ChevronRight, PlayCircle } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { useToast } from "@/components/ui/use-toast";

const slides = [
  {
    id: 1,
    title: "Build Together, Grow Together",
    description: "Join httpstack, a vibrant community where developers connect, collaborate, and build innovative solutions for commercial success.",
    imageText: "A vibrant and sprawling futuristic city under a twilight sky, with flying vehicles and towering neon-lit skyscrapers",
  },
  {
    id: 2,
    title: "Empowering Your Projects",
    description: "httpstack provides the tools and network you need to transform your ideas into profitable ventures. Unleash your potential.",
    imageText: "A group of determined anime heroes standing together on a cliff edge, looking out at a dramatic sunrise",
  },
  {
    id: 3,
    title: "Your Stack, Your Success",
    description: "From open-source contributions to commercial partnerships, httpstack is the platform where your code makes an impact.",
    imageText: "An anime character with glowing eyes and hands, surrounded by crackling energy, ready to unleash a powerful attack",
  },
];

const slideVariants = {
  enter: (direction) => ({
    x: direction > 0 ? 1000 : -1000,
    opacity: 0,
  }),
  center: {
    zIndex: 1,
    x: 0,
    opacity: 1,
  },
  exit: (direction) => ({
    zIndex: 0,
    x: direction < 0 ? 1000 : -1000,
    opacity: 0,
  }),
};

const HeroSection = () => {
    const [[page, direction], setPage] = useState([0, 0]);
    const { toast } = useToast();

    const paginate = (newDirection) => {
        setPage([(page + newDirection + slides.length) % slides.length, newDirection]);
    };

    const handleWatchNow = () => {
        toast({
            title: "🚀 Coming Soon!",
            description: "The full experience is not yet available, but stay tuned for epic adventures!",
        });
    };

    useEffect(() => {
        const interval = setInterval(() => {
            paginate(1);
        }, 5000); // Change slide every 5 seconds
        return () => clearInterval(interval);
    }, [page]);


    return (
        <section id="home" className="relative min-h-screen flex items-center justify-center overflow-hidden">
            <div className="absolute inset-0 z-0">
                <AnimatePresence initial={false} custom={direction}>
                    <motion.div
                        key={page}
                        className="absolute inset-0 w-full h-full"
                        custom={direction}
                        variants={slideVariants}
                        initial="enter"
                        animate="center"
                        exit="exit"
                        transition={{
                            x: { type: "spring", stiffness: 300, damping: 30 },
                            opacity: { duration: 0.2 }
                        }}
                    >
                        <img  className="w-full h-full object-cover" alt={slides[page].title} src="https://images.unsplash.com/photo-1691527385266-62295bbcabb1" />
                    </motion.div>
                </AnimatePresence>
                <div className="absolute inset-0 bg-black/60"></div>
            </div>

            <div className="relative z-10 container mx-auto px-6 text-center flex flex-col items-center">
                 <AnimatePresence mode="wait">
                    <motion.div
                        key={page}
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -20 }}
                        transition={{ duration: 0.5 }}
                        className="w-full max-w-2xl"
                    >
                        <Card className="bg-black/50 backdrop-blur-sm border-purple-500/50 text-white shadow-2xl shadow-purple-500/20">
                            <CardHeader>
                                <CardTitle className="text-4xl md:text-5xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-purple-500">
                                    {slides[page].title}
                                </CardTitle>
                                <CardDescription className="text-gray-300 text-lg mt-2">
                                    {slides[page].description}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <Button size="lg" className="bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-600 hover:to-purple-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transform hover:scale-105 transition-transform" onClick={handleWatchNow}>
                                    <PlayCircle className="mr-2 h-5 w-5" />
                                    Learn More
                                </Button>
                            </CardContent>
                        </Card>
                    </motion.div>
                </AnimatePresence>
            </div>
            
            <div className="absolute z-20 top-1/2 -translate-y-1/2 left-4">
                <button onClick={() => paginate(-1)} className="p-2 bg-white/20 rounded-full hover:bg-white/40 transition-colors">
                    <ChevronLeft className="w-8 h-8 text-white"/>
                </button>
            </div>
            <div className="absolute z-20 top-1/2 -translate-y-1/2 right-4">
                <button onClick={() => paginate(1)} className="p-2 bg-white/20 rounded-full hover:bg-white/40 transition-colors">
                    <ChevronRight className="w-8 h-8 text-white"/>
                </button>
            </div>
        </section>
    );
};

export default HeroSection;