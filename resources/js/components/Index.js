import React, {useState} from 'react';
import ReactDOM from 'react-dom';

function Index() {

    const [initialized, setInitialized] = useState(false)

    return (
        <div className="container">
            TEst
        </div>
    );
}

export default Index;

if (document.getElementById('root')) {
    ReactDOM.render(
        <Index/>
        , document.getElementById('root')
    );
}
