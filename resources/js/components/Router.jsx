import {AnimatePresence} from "framer-motion";
import {Route, Routes, useLocation} from "react-router";
import Email from "./Email";
import Login from "./Login/Login";
import Register from "./Register/Register";
import Password from "./Recovery/Password";
import TwoStep from "./Recovery/TwoStep";
import Authorization from "./OAuth2/Authorization";
import Confirm from "./Confirm/Confirm";
import SwitchUser from "./SwitchUser";

const Router = () => {

    const location = useLocation()

    return (
        <AnimatePresence mode="wait">
            <Routes location={location} key={location.pathname}>
                // Base routes
                <Route path={"/"} element={<Email/>}/>
                <Route path={"/login"} element={<Login/>}/>
                <Route path={"/register"} element={<Register/>}/>
                <Route path={"/switch-user"} element={<SwitchUser/>}/>

                // Authorize
                <Route path={"/authorize"} element={<Confirm/>}/>

                //OAuth2
                <Route path={"/oauth/authorize"} element={<Authorization/>}/>

                // Recovery
                <Route path={"/recovery/password"} element={<Password/>}/>
                <Route path={"/recovery/two-step"} element={<TwoStep/>}/>
            </Routes>
        </AnimatePresence>
    )
}

export default Router
