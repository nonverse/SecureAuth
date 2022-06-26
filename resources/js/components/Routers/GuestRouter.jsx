import {useLocation, Routes, Route} from "react-router-dom";
import {AnimatePresence} from "framer-motion";
import Fluid from "../elements/Fluid";
import Email from "../Email";
import {useState} from "react";
import Login from "../Login/Login";
import Register from "../Register/Register";
import PasswordRecovery from "../Recovery/PasswordRecovery";
import TwoFactorRecovery from "../Recovery/TwoFactorRecovery";
import ConfirmPassword from "../ConfirmPassword";

const GuestRouter = ({setInitialized}) => {

    const [user, setUser] = useState({})
    const query = new URLSearchParams(window.location.search)
    const intended = {
        host: query.get('host'),
        resource: query.get('resource')
    }
    const location = useLocation()

    return (
        <AnimatePresence exitBeforeEnter>
            <Routes location={location} key={location.pathname}>
                <Route path={'/'} element={<Fluid/>}>
                    // Authentication routes
                    <Route exact path={'/'} element={<Email setUser={setUser} setInitialized={setInitialized}/>}/>
                    <Route path={'/login'}
                           element={<Login user={user} setUser={setUser} setInitialized={setInitialized}/>}/>
                    <Route path={'/register'}
                           element={<Register user={user} setUser={setUser} setInitialized={setInitialized}/>}/>

                    // Recovery Routes
                    <Route path={'/recovery/password'}
                           element={<PasswordRecovery user={user} setUser={setUser} setInitialized={setInitialized}/>}/>
                    <Route path={'/recovery/two-factor'}
                           element={<TwoFactorRecovery user={user} setUser={setUser} setInitialized={setInitialized}
                                                       intended={intended}/>}/>
                </Route>
            </Routes>
        </AnimatePresence>
    )
}

export default GuestRouter;
