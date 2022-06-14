import React from 'react';
import ReactDOM from 'react-dom';
import Logo from "./elements/Logo";

function Index() {
    return (
        <div className="container">
            <Logo color={"#333344"}/>
        </div>
    );
}

export default Index;

if (document.getElementById('root')) {
    ReactDOM.render(<Index/>, document.getElementById('root'));
}
