import React, {useState} from 'react';
import ReactDOM from 'react-dom';
import Logo from "../elements/Logo";
import {BrowserRouter} from "react-router-dom";
import Loader from "./Loader";

function Index() {

    const [initialised, setInitialised] = useState(true)

    return (
        <div className="app">
            {initialised ?
                <>
                    <Logo/>
                    <BrowserRouter>
                        <div className="container">

                        </div>
                    </BrowserRouter>
                </>
                : <Loader/>
            }
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
