
const Footer = ({ socialLinks }) => (
    <footer>
        <div className="social-links">
            {socialLinks.map((link, index) => (
                <a key={index} href={link.href} target="_blank" rel="noopener noreferrer">
                    <i className={`fab fa-${link.icon}`}></i> {/* Font Awesome icon */}
                </a>
            ))}
        </div>
        <p>&copy; {new Date().getFullYear()} httpstack.tech | <a href="#">Privacy Policy</a> | <a href="#">TOS</a></p>
    </footer>
);
export default Footer;