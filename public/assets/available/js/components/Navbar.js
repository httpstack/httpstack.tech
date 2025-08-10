const Navbar = ({ links }) => (
    <nav>
        <ul>
            {links.map((link, index) => (
                <li key={index}>
                    <a href={link.href}>{link.text}</a>
                </li>
            ))}
        </ul>
    </nav>
);
export default Navbar;