import React, {useEffect, useState} from 'react';
import ReactDOM from 'react-dom';
import Logo from "../elements/Logo";
import {BrowserRouter} from "react-router-dom";
import Loader from "./Loader";
import {Provider, useDispatch, useSelector} from "react-redux";
import store from "../state/store";
import Router from "./Router";
import {auth} from "../scripts/api/auth";
import {updateUser} from "../state/user";

function Index() {

    const [initialised, setInitialised] = useState(false)
    const loading = useSelector(state => state.loader.value)
    const dispatch = useDispatch()

    useEffect(async () => {
        if (window.location.pathname === '/login') {
            await auth.get('api/user/cookie')
                .then(response => {
                    dispatch(updateUser(response.data.data))
                    setInitialised(true)
                })
        } else {
            setInitialised(true)
        }
    }, [])

    return (
        <div className="app">
            {initialised ?
                <>
                    <Logo/>
                    <BrowserRouter>
                        <div className="container">
                            {loading ? <Loader/> : ''}
                            <Router/>
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
