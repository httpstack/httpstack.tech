import React from 'react';
import PageWrapper from '@/components/PageWrapper';
import { motion } from 'framer-motion';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Code, Layers, Layout, Globe, Server, Database, Settings, Package, TestTube, BookOpen, Rocket } from 'lucide-react';

const architecturalLayers = [
    { name: "Client", type: "Architectural", icon: <Code className="w-8 h-8 text-blue-400" />, color: "Light Blue", rationale: "The user-facing component, where the experience begins. Cool and clear." },
    { name: "HTML", type: "Architectural", icon: <Layers className="w-8 h-8 text-gray-300" />, color: "Off-White", rationale: "The structural skeleton of the content. The \"paper\" upon which everything is written." },
    { name: "CSS", type: "Architectural", icon: <Layout className="w-8 h-8 text-teal-400" />, color: "Teal", rationale: "The Style and presentation. The aesthetic and finishing touches." },
    { name: "JavaScript (JS)", type: "Architectural", icon: <Globe className="w-8 h-8 text-yellow-400" />, color: "Golden Yellow", rationale: "The dynamic, interactive energy of the application. The spark of life." },
    { name: "Web/File Server", type: "Architectural", icon: <Server className="w-8 h-8 text-slate-400" />, color: "Slate Gray", rationale: "The frontline workhorse. The reliable gatekeeper that handles incoming requests." },
    { name: "Programming Model", type: "Architectural", icon: <Package className="w-8 h-8 text-purple-600" />, color: "Dark Purple", rationale: "The Logic. The deep, complex thinking and business rules. The brain of the operation." },
    { name: "Data Sources", type: "Architectural", icon: <Database className="w-8 h-8 text-red-600" />, color: "Deep Red", rationale: "The foundational data store. The persistent, lifeblood \"heart\" of the application." },
    { name: "Operating System", type: "Architectural", icon: <Settings className="w-8 h-8 text-gray-600" />, color: "Dark Slate Gray", rationale: "The bedrock foundation upon which all server-side software runs. The deepest, most solid layer." },
];

const operationalLayers = [
    { name: "Productivity & Admin", type: "Operational", icon: <Rocket className="w-8 h-8 text-orange-600" />, color: "Burnt Orange", rationale: "The developer's tools. Represents craftsmanship, focus, and the environment where work gets done." },
    { name: "Testing", type: "Operational", icon: <TestTube className="w-8 h-8 text-green-500" />, color: "Bright Green", rationale: "The \"Go/No-Go\" signal. Represents quality assurance, health, and the confidence to proceed." },
    { name: "Documentation", type: "Operational", icon: <BookOpen className="w-8 h-8 text-amber-200" />, color: "Parchment Beige", rationale: "The comprehensive guide to the stack. Ensures clarity and maintainability." },
    { name: "Deployment & Maintenance", type: "Operational", icon: <Rocket className="w-8 h-8 text-blue-700" />, color: "Steel Blue", rationale: "The CI/CD pipelines and infrastructure management. The heavy machinery that moves the code to production." },
];

const StackSpecPage = () => {
    return (
        <PageWrapper
            title="Stack Spec System"
            description="Explore httpstack's 12-Layer 'Stack Spec' System and the 'Become a Specifier' campaign."
        >
            <motion.section
                initial={{ opacity: 0, y: 50 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8 }}
                className="container mx-auto px-6 py-16 text-center"
            >
                <h1 className="text-5xl md:text-6xl font-extrabold mb-8 bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-purple-500">
                    The 12-Layer 'Stack Spec' System
                </h1>
                <p className="text-xl text-gray-300 max-w-3xl mx-auto mb-12">
                    We categorize your 12 layers into two distinct types for clarity and power:
                    <br />
                    <span className="font-semibold">1. Architectural Layers:</span> The core components of the running application itself.
                    <br />
                    <span className="font-semibold">2. Operational Layers:</span> The tools and processes that enable the stack's creation, validation, and maintenance.
                </p>

                <h2 className="text-4xl font-bold mb-8 text-cyan-400">Architectural Layers (The Runtime Core)</h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    {architecturalLayers.map((layer, index) => (
                        <motion.div
                            key={layer.name}
                            initial={{ opacity: 0, scale: 0.9 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ duration: 0.5, delay: index * 0.1 }}
                        >
                            <Card className="bg-gray-800/70 border-purple-500/30 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
                                <CardHeader className="flex flex-row items-center justify-between pb-2">
                                    <CardTitle className="text-2xl font-bold text-cyan-300">{layer.name}</CardTitle>
                                    {layer.icon}
                                </CardHeader>
                                <CardContent className="text-left">
                                    <p className="text-sm text-gray-400 mb-2">Type: {layer.type}</p>
                                    <p className="text-sm text-gray-400 mb-4">Color: {layer.color}</p>
                                    <CardDescription className="text-gray-200">{layer.rationale}</CardDescription>
                                </CardContent>
                            </Card>
                        </motion.div>
                    ))}
                </div>

                <h2 className="text-4xl font-bold mb-8 text-cyan-400">Operational Layers (The Development Lifecycle)</h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    {operationalLayers.map((layer, index) => (
                        <motion.div
                            key={layer.name}
                            initial={{ opacity: 0, scale: 0.9 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ duration: 0.5, delay: index * 0.1 }}
                        >
                            <Card className="bg-gray-800/70 border-purple-500/30 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
                                <CardHeader className="flex flex-row items-center justify-between pb-2">
                                    <CardTitle className="text-2xl font-bold text-cyan-300">{layer.name}</CardTitle>
                                    {layer.icon}
                                </CardHeader>
                                <CardContent className="text-left">
                                    <p className="text-sm text-gray-400 mb-2">Type: {layer.type}</p>
                                    <p className="text-sm text-gray-400 mb-4">Color: {layer.color}</p>
                                    <CardDescription className="text-gray-200">{layer.rationale}</CardDescription>
                                </CardContent>
                            </Card>
                        </motion.div>
                    ))}
                </div>

                <h2 className="text-4xl font-bold mb-8 bg-clip-text text-transparent bg-gradient-to-r from-purple-400 to-pink-500">
                    The Campaign: "Become a Specifier"
                </h2>
                <p className="text-xl text-gray-300 max-w-3xl mx-auto mb-12">
                    This campaign is about empowerment and creating a movement. We aren't just offering a documentation tool; we are inviting developers to become the architects and standard-bearers of the future of web development.
                </p>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                    <motion.div
                        initial={{ opacity: 0, x: -50 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.7, delay: 0.2 }}
                    >
                        <Card className="bg-gray-800/70 border-purple-500/30 text-white shadow-lg">
                            <CardHeader>
                                <CardTitle className="text-3xl font-bold text-cyan-300">Core Message</CardTitle>
                            </CardHeader>
                            <CardContent className="text-left">
                                <p className="text-lg text-gray-200 mb-4">
                                    Stop just <span className="font-semibold text-cyan-400">using stacks</span>. Start <span className="font-semibold text-cyan-400">defining them</span>. A documented stack is a solved problem. A specified stack is an industry standard. <span className="font-bold text-purple-400">Become a Specifier.</span>
                                </p>
                            </CardContent>
                        </Card>
                    </motion.div>
                    <motion.div
                        initial={{ opacity: 0, x: 50 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.7, delay: 0.4 }}
                    >
                        <Card className="bg-gray-800/70 border-purple-500/30 text-white shadow-lg">
                            <CardHeader>
                                <CardTitle className="text-3xl font-bold text-cyan-300">Target Audience & Messaging</CardTitle>
                            </CardHeader>
                            <CardContent className="text-left text-lg text-gray-200">
                                <ul className="list-disc list-inside space-y-3">
                                    <li><span className="font-semibold text-purple-300">Senior Developers & Architects:</span> "Tired of inconsistent docs and fragile integrations? With <span className="font-bold">Stack-Wrangler</span>, you can codify your team's best practices into a repeatable, verifiable standard. Specify your stack, reduce onboarding time, and build with confidence."</li>
                                    <li><span className="font-semibold text-purple-300">Mid-Level Developers:</span> "You've built with LAMP, MERN, and PERN. Now, it's time to build your stack. Use <span className="font-bold">Stack-Wrangler</span> to explore unorthodox combinations, get an instant compatibility score, and generate the full workflow to make it work. Go beyond the acronyms."</li>
                                    <li><span className="font-semibold text-purple-300">Open Source Maintainers:</span> "Give your contributors the ultimate guide. A <span className="font-bold">Stack-Wrangler</span> specification is the most comprehensive CONTRIBUTING.md you'll ever write. Attract more talent with crystal-clear documentation."</li>
                                    <li><span className="font-semibold text-purple-300">Hiring Managers & Recruiters:</span> "Ask candidates for their <span className="font-bold">Stack-Wrangler</span> profile. Instantly see the architectures they've mastered and specified, complete with documentation bands proving their thoroughness."</li>
                                </ul>
                            </CardContent>
                        </Card>
                    </motion.div>
                </div>

                <h2 className="text-4xl font-bold mb-8 text-cyan-400">Campaign Rollout Plan</h2>
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <motion.div
                        initial={{ opacity: 0, y: 50 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.7, delay: 0.6 }}
                    >
                        <Card className="bg-gray-800/70 border-purple-500/30 text-white shadow-lg">
                            <CardHeader>
                                <CardTitle className="text-3xl font-bold text-cyan-300">Phase 1: The Foundation (First 3 Months)</CardTitle>
                            </CardHeader>
                            <CardContent className="text-left text-lg text-gray-200">
                                <ul className="list-disc list-inside space-y-2">
                                    <li><span className="font-semibold">Seed the Platform:</span> Before launch, we will create the first 20 "Official Stack Specs" for the most popular stacks (MERN, PERN, LAMP, etc.) to serve as gold-standard examples.</li>
                                    <li><span className="font-semibold">Recruit "Founding Specifiers":</span> Well hand-pick 50 influential developers, give them early access, and task them with creating some "unorthodox but viable" stack spec each.</li>
                                    <li><span className="font-semibold">Content Blitz:</span> Publish key blog posts on Dev.to and our "Tech-Heaps" blog:
                                        <ul className="list-disc list-inside ml-4 text-sm text-gray-300">
                                            <li>"Why Your README Isn't Enough: Introducing the Stack Spec"</li>
                                            <li>"We Color-Coded the Entire Web Stack. Here's Why."</li>
                                            <li>"Benchmarking the Un-benchmarkable: A Guide to Testing Weird Stacks."</li>
                                        </ul>
                                    </li>
                                </ul>
                            </CardContent>
                        </Card>
                    </motion.div>
                    <motion.div
                        initial={{ opacity: 0, y: 50 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.7, delay: 0.8 }}
                    >
                        <Card className="bg-gray-800/70 border-purple-500/30 text-white shadow-lg">
                            <CardHeader>
                                <CardTitle className="text-3xl font-bold text-cyan-300">Phase 2: Community Growth (Months 3-9)</CardTitle>
                            </CardHeader>
                            <CardContent className="text-left text-lg text-gray-200">
                                <ul className="list-disc list-inside space-y-2">
                                    <li><span className="font-semibold">Public Launch:</span> Launch on Product Hunt, Hacker News, and relevant subreddits. The headline will be: "<span className="font-bold">Stack-Wrangler</span>: A WHATWG for your entire tech stack."</li>
                                    <li><span className="font-semibold">"Spec of the Week":</span> Each week, feature the most interesting or well-documented stack submitted to the community. The creator gets a "Certified Specifier" badge.</li>
                                    <li><span className="font-semibold">The Compatibility Engine:</span> Heavily market the "likelihood of working" score. This is the magic that draws people in.</li>
                                    <li><span className="font-semibold">Generate & Share:</span> Make the documentation bands and stack formulas incredibly easy to share on Twitter, LinkedIn, and GitHub READMEs to create a viral loop.</li>
                                </ul>
                            </CardContent>
                        </Card>
                    </motion.div>
                    <motion.div
                        initial={{ opacity: 0, y: 50 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.7, delay: 1.0 }}
                    >
                        <Card className="bg-gray-800/70 border-purple-500/30 text-white shadow-lg">
                            <CardHeader>
                                <CardTitle className="text-3xl font-bold text-cyan-300">Phase 3: Solidifying the Standard (Month 9 Onward)</CardTitle>
                            </CardHeader>
                            <CardContent className="text-left text-lg text-gray-200">
                                <ul className="list-disc list-inside space-y-2">
                                    <li><span className="font-semibold">Partnerships:</span> Work with cloud providers (AWS, Azure, Google Cloud) to integrate <span className="font-bold">Stack-Wrangler</span>. Imagine a "Deploy with Stack-Wrangler" button that uses the generated workflow to spin up the entire specified infrastructure.</li>
                                    <li><span className="font-semibold">Open Source Integration:</span> Create a GitHub Action that checks a project's documentation band. If a pull request merges without updating docs, the band's score for that layer drops, visually flagging the new "documentation debt."</li>
                                    <li><span className="font-semibold">The "State of Stacks" Report:</span> Annually, use anonymized data to publish a definitive report on architectural trends, cementing <span className="font-bold">Stack-Wrangler</span> as an industry authority.</li>
                                </ul>
                                <p className="mt-4 text-sm text-gray-300">
                                    This is the path from a great idea to an industry-defining platform. We start by building a beautiful, functional language for describing technology, and then we empower the community to use that language to build and share their knowledge.
                                </p>
                            </CardContent>
                        </Card>
                    </motion.div>
                </div>
            </motion.section>
        </PageWrapper>
    );
};

export default StackSpecPage;