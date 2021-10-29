import React from "react";
import {BrowserRouter, Switch, Route, Redirect} from "react-router-dom";
import LoginForm from "../components/Login/LoginForm";
import RegisterForm from "../components/Register/RegisterForm";

const AuthRouter = ({load}) => {

    return (
        <BrowserRouter>
            <Switch>
                {/*Redirect blank route to login form*/}
                <Route exact path={'/'}>
                    <Redirect to={'/login'}/>
                </Route>

                {/*Authentication Routes*/}
                <Route path={'/login'} render={(props) => <LoginForm {...props} load={load}/>}/>
                <Route path={'/register'} render={(props) => <RegisterForm {...props} load={load}/>}/>

            </Switch>
        </BrowserRouter>
    )
}

export default AuthRouter
