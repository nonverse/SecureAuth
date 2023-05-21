import React, {useState} from 'react';
import ReactDOM from 'react-dom';
import Logo from "../elements/Logo";
import {BrowserRouter} from "react-router-dom";
import Loader from "./Loader";
import Email from "./Email";
import {Provider} from "react-redux";
import store from "../state/store";

function Index() {

    const [initialised, setInitialised] = useState(true)

    return (
        <div className="app">
            {initialised ?
                <>
                    <Logo/>
                    <BrowserRouter>
                        <div className="container">
                            <Email/>
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
        <Provider store={store}>
            <Index/>
        </Provider>
        , document.getElementById('root')
    );
}
