function MyComponent(props) {
    // Use the props passed down from the PHP backend
    const { user_name, welcome_message } = props;

    const [count, setCount] = React.useState(0);

    return (
        <div>
            <h1>Welcome, {user_name || 'Guest'}!</h1>
            <p>{welcome_message || 'This is your React component.'}</p>
            <p>You clicked the button {count} times.</p>
            <button onClick={() => setCount(count + 1)}>
                Click me
            </button>
        </div>
    );
}
