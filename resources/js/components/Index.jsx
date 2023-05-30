import React, {useState} from 'react';
import ReactDOM from 'react-dom';
import Logo from "./elements/Logo";
import {BrowserRouter} from "react-router-dom";
import GuestRouter from "./Routers/GuestRouter";
import Loader from "./Loader";
import {Provider} from "react-redux";
import store from "../state/store";
import AuthenticatedRouter from "./Routers/AuthenticatedRouter";

function Index() {

    const [initialized, setInitialized] = useState(false)

    return (
        <div className="container">
            <Logo color={"#333344"}/>
            <BrowserRouter>
                <GuestRouter setInitialized={setInitialized}/>
                <AuthenticatedRouter setInitialized={setInitialized}/>
            </BrowserRouter>
            {initialized ? '' : <Loader/>}
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
