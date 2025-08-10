const root = document.getElementById('root');

const navLinks = [
    { href: '#about', text: 'About' },
    { href: '#projects', text: 'Projects' },
    { href: '#contact', text: 'Contact' },
];

const socialLinks = [
    { href: 'https://www.linkedin.com', text: 'LinkedIn', icon: 'linkedin' },
    { href: 'https://github.com', text: 'GitHub', icon: 'github' },
    // Add more social links as needed
];
const appTitle = "HttpStack.tech";
const Header = () => {
    return (
        <div>
            {appTitle}
        </div>
    );
}
const App = () => {
    return (
        <Header />
    );
};

ReactDOM.render(<App />, root);