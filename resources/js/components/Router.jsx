import {AnimatePresence} from "framer-motion";
import {Route, Routes, useLocation} from "react-router";
import Email from "./Email";
import Password from "./Login/Password";

const Router = () => {

    const location = useLocation()

    return (
        <AnimatePresence mode="wait">
            <Routes location={location} key={location.pathname}>
                <Route path={"/"} element={<Email/>}/>
                <Route path={"/login"} element={<Password/>}/>
            </Routes>
        </AnimatePresence>
    )
}

export default Router
