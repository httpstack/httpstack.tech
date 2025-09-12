import React from 'react';
import { motion } from 'framer-motion';
import { Helmet } from 'react-helmet';

const pageVariants = {
    initial: {
        opacity: 0,
        y: 20,
    },
    in: {
        opacity: 1,
        y: 0,
    },
    out: {
        opacity: 0,
        y: -20,
    },
};

const pageTransition = {
    type: 'tween',
    ease: 'anticipate',
    duration: 0.5,
};

const PageWrapper = ({ children, title, description }) => {
    return (
        <>
            <Helmet>
                <title>{title} | httpstack</title>
                <meta name="description" content={description} />
                <meta property="og:title" content={`${title} | httpstack`} />
                <meta property="og:description" content={description} />
            </Helmet>
            <motion.div
                initial="initial"
                animate="in"
                exit="out"
                variants={pageVariants}
                transition={pageTransition}
            >
                {children}
            </motion.div>
        </>
    );
};

export default PageWrapper;