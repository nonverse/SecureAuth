import React, {useState} from 'react';
import ReactDOM from 'react-dom';
import Logo from "./elements/Logo";
import {BrowserRouter} from "react-router-dom";
import Router from "./Router";
import Loader from "./Loader";

function Index() {

    const[initialized, setInitialized] = useState(false)

    return (
        <div className="container">
            <Logo color={"#333344"}/>
            <BrowserRouter>
                <Router setInitialized={setInitialized}/>
            </BrowserRouter>
            {initialized ? '' : <Loader/>}
        </div>
    );
}

export default Index;

if (document.getElementById('root')) {
    ReactDOM.render(<Index/>, document.getElementById('root'));
}
