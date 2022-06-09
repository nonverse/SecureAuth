import React from 'react';
import ReactDOM from 'react-dom';

function Index() {
    return (
        <div className="container">
            <h1>Hello</h1>
        </div>
    );
}

export default Index;

if (document.getElementById('root')) {
    ReactDOM.render(<Index/>, document.getElementById('root'));
}
