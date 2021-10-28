import React from 'react';
import ReactDOM from 'react-dom';

function Index() {
    return (
        <div className="app">
            <h1>SecureAuth App</h1>
        </div>
    );
}

export default Index;

if (document.getElementById('root')) {
    ReactDOM.render(<Index />, document.getElementById('root'));
}
