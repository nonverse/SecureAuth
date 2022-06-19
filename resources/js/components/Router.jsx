import {useLocation, Routes, Route} from "react-router-dom";
import {AnimatePresence} from "framer-motion";
import Fluid from "./elements/Fluid";
import Email from "./Email";
import {useState} from "react";
import Login from "./Login/Login";
import Register from "./Register/Register";

const Router = () => {

    const [user, setUser] = useState({})
    const location = useLocation()

    return (
        <AnimatePresence exitBeforeEnter>
            <Routes location={location} key={location.pathname}>
                <Route path={'/'} element={<Fluid/>}>
                    <Route exact path={'/'} element={<Email setUser={setUser}/>}/>
                    <Route path={'/login'} element={<Login user={user} setUser={setUser}/>}/>
                    <Route path={'/register'} element={<Register user={user} setUser={setUser}/>}/>
                </Route>
            </Routes>
        </AnimatePresence>
    )
}

export default Router;
