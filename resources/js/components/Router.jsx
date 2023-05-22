import {AnimatePresence} from "framer-motion";
import {Route, Routes, useLocation} from "react-router";
import Email from "./Email";
import Login from "./Login/Login";
import Register from "./Register/Register";

const Router = () => {

    const location = useLocation()

    return (
        <AnimatePresence mode="wait">
            <Routes location={location} key={location.pathname}>
                <Route path={"/"} element={<Email/>}/>
                <Route path={"/login"} element={<Login/>}/>
                <Route path={"/register"} element={<Register/>}/>
            </Routes>
        </AnimatePresence>
    )
}

export default Router
