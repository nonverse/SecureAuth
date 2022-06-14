import React from 'react';
import ReactDOM from 'react-dom';
import Fluid from "./elements/Fluid";
import Logo from "./elements/Logo";
import {BrowserRouter} from "react-router-dom";
import Router from "./Router";

function Index() {
    return (
        <div className="container">
            <Logo color={"#333344"}/>
            <BrowserRouter>
                <Router/>
            </BrowserRouter>
        </div>
    );
}

export default Index;

if (document.getElementById('root')) {
    ReactDOM.render(<Index/>, document.getElementById('root'));
}
