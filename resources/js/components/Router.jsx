import {AnimatePresence} from "framer-motion";
import {Route, Routes, useLocation} from "react-router";
import Email from "./Email";
import Login from "./Login/Login";
import Register from "./Register/Register";
import Password from "./Recovery/Password";

const Router = () => {

    const location = useLocation()

    return (
        <AnimatePresence mode="wait">
            <Routes location={location} key={location.pathname}>
                // Base routes
                <Route path={"/"} element={<Email/>}/>
                <Route path={"/login"} element={<Login/>}/>
                <Route path={"/register"} element={<Register/>}/>

                // Recovery
                <Route path={"/recovery/password"} element={<Password/>}/>
            </Routes>
        </AnimatePresence>
    )
}

export default Router
