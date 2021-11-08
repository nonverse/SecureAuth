import React from "react";
import {BrowserRouter, Switch, Route, Redirect} from "react-router-dom";
import LoginForm from "../components/Login/LoginForm";
import RegisterForm from "../components/Register/RegisterForm";
import ForgotPasswordForm from "../components/Recovery/ForgotPasswordForm";
import ResetPasswordForm from "../components/Recovery/ResetPasswordForm";

const AuthRouter = ({load, setInitialised}) => {

    return (
        <BrowserRouter>
            <Switch>
                {/*Redirect blank route to login form*/}
                <Route exact path={'/'}>
                    <Redirect to={'/login'}/>
                </Route>

                {/*Authentication Routes*/}
                <Route path={'/login'} render={(props) => <LoginForm {...props} load={load} setInitialised={setInitialised}/>}/>
                <Route path={'/register'} render={(props) => <RegisterForm {...props} load={load} setInitialised={setInitialised}/>}/>
                <Route path={'/forgot'} render={(props) => <ForgotPasswordForm {...props} load={load} setInitialised={setInitialised}/>}/>
                <Route path={'/reset'} render={(props) => <ResetPasswordForm {...props} load={load} setInitialised={setInitialised}/>}/>

            </Switch>
        </BrowserRouter>
    )
}

export default AuthRouter
