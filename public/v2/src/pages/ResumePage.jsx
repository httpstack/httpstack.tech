import React from 'react';
import PageWrapper from '@/components/PageWrapper';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { motion } from 'framer-motion';
import { Download, Eye, FileText } from 'lucide-react';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { useToast } from "@/components/ui/use-toast";

const ResumePage = () => {
    const { toast } = useToast();

    const handleDownload = (format) => {
        toast({
            title: `🚧 Download (${format})`,
            description: "This feature isn't implemented yet—but don't worry! You can request it in your next prompt! 🚀",
        });
    };

    return (
        <PageWrapper title="Resume" description="View and download my professional resume.">
            <div className="container mx-auto px-6 py-20 md:py-28 flex items-center justify-center min-h-[70vh]">
                <motion.div
                    initial={{ opacity: 0, scale: 0.9 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ duration: 0.5 }}
                >
                    <Card className="w-full max-w-3xl bg-black/40 backdrop-blur-md border-purple-500/30 text-center p-8">
                        <CardContent className="flex flex-col items-center gap-6">
                            <FileText className="w-24 h-24 text-cyan-400" />
                            <h1 className="text-4xl md:text-5xl font-bold">My Resume</h1>
                            <p className="text-lg text-gray-300 max-w-md">
                                Here you can view my resume online or download it in your preferred format.
                            </p>
                            <div className="flex flex-wrap justify-center gap-4 mt-4">
                                <Dialog>
                                    <DialogTrigger asChild>
                                        <Button size="lg" className="bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-600 hover:to-purple-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transform hover:scale-105 transition-transform">
                                            <Eye className="mr-2 h-5 w-5" />
                                            View (Lightbox)
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent className="sm:max-w-[825px] bg-gray-900 border-cyan-500 text-white">
                                        <DialogHeader>
                                            <DialogTitle>My Resume</DialogTitle>
                                        </DialogHeader>
                                        <div className="mt-4 h-[70vh] bg-white rounded-md p-4">
                                            <p className="text-black">Resume content would be displayed here. This could be an embedded PDF or an image.</p>
                                            <p className="text-black mt-4">For now, this is a placeholder. You can request to have your actual resume embedded!</p>
                                        </div>
                                    </DialogContent>
                                </Dialog>

                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button size="lg" variant="outline" className="font-bold py-3 px-8 rounded-full shadow-lg transform hover:scale-105 transition-transform border-cyan-400 text-cyan-400 hover:bg-cyan-400/10 hover:text-cyan-300">
                                            <Download className="mr-2 h-5 w-5" />
                                            Download
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent className="bg-gray-800 border-purple-500 text-white">
                                        <DropdownMenuItem onClick={() => handleDownload('PDF')} className="cursor-pointer hover:!bg-purple-600">PDF</DropdownMenuItem>
                                        <DropdownMenuItem onClick={() => handleDownload('DOCX')} className="cursor-pointer hover:!bg-purple-600">DOCX</DropdownMenuItem>
                                        <DropdownMenuItem onClick={() => handleDownload('TXT')} className="cursor-pointer hover:!bg-purple-600">TXT</DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </CardContent>
                    </Card>
                </motion.div>
            </div>
        </PageWrapper>
    );
};

export default ResumePage;