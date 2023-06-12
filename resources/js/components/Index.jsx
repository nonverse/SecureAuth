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
import validate from "../scripts/validate";
import {updateClient} from "../state/client";

function Index() {

    const [initialised, setInitialised] = useState(false)
    const loading = useSelector(state => state.loader.value)
    const query = new URLSearchParams(window.location.search)
    const dispatch = useDispatch()

    useEffect(async () => {
        if (window.location.pathname === '/login') {
            await auth.get('/user/cookie')
                .then(response => {
                    dispatch(updateUser(response.data.data))
                    setInitialised(true)
                })
        } else if (window.location.pathname === '/register') {
            if (validate.email(query.get('email'))) {
                window.location.replace('/')
            } else {
                dispatch(updateUser({
                    email: query.get('email')
                }))
            }
            setInitialised(true)
        } else if (window.location.pathname === '/recovery/two-step') {
            if (validate.require(query.get('token'), 64, 64)) {
                window.location.replace('/')
            }
            setInitialised(true)
        } else if (window.location.pathname === '/oauth/authorize') {
            await auth.post('/oauth/authorize/validate-client', query)
                .then(response => {
                    if (response.data.data.approved) {
                        return window.location = `${query.get('redirect_uri')}?code=${response.data.data.code}`
                    }
                    dispatch(updateClient(response.data.data))
                    setInitialised(true)
                })
                .catch(() => {
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
            <span id="signature">Micky & Rex Co<span className="splash">.</span></span>
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
