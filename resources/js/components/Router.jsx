import {useLocation, Routes, Route} from "react-router-dom";
import {AnimatePresence} from "framer-motion";
import Fluid from "./elements/Fluid";
import Email from "./Email";
import {useState} from "react";

const Router = () => {

    const [user, setUser] = useState({})
    const location = useLocation()

    return (
        <AnimatePresence exitBeforeEnter>
            <Routes location={location} key={location.pathname}>
                <Route path={'/'} element={<Fluid/>}>
                    <Route exact path={'/'} element={<Email setUser={setUser}/>}/>
                </Route>
            </Routes>
        </AnimatePresence>
    )
}

export default Router;
