import {useLocation, Routes, Route} from "react-router-dom";
import {AnimatePresence} from "framer-motion";
import Fluid from "../elements/Fluid";
import {useEffect, useState} from "react";
import {auth} from "../../../scripts/api/auth";
import ConfirmPassword from "../ConfirmPassword";

const AuthenticatedRouter = ({setInitialized}) => {

    const [user, setUser] = useState({})
    const location = useLocation()

    useEffect(async () => {
        await auth.get('api/user')
            .then((response) => {
                setUser(response.data.data)
            })
    })

    return (
        <AnimatePresence exitBeforeEnter>
            <Routes location={location} key={location.pathname}>
                <Route path={'/'} element={<Fluid/>}>
                    <Route path={'/confirm'} element={<ConfirmPassword user={user} setInitialized={setInitialized}/>}/>
                </Route>
            </Routes>
        </AnimatePresence>
    )
}

export default AuthenticatedRouter;
