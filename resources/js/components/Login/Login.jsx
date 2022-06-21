import {useEffect, useState} from "react";
import ProgressiveForm from "../elements/ProgressiveForm";
import Password from "./Password";
import TwoFactor from "./TwoFactor";
import {auth} from "../../../scripts/api/auth";

const Login = ({user, setUser, setInitialized}) => {

    const [state, setState] = useState(1)

    useEffect(async () => {
        await auth.get('api/user/cookie')
            .then(response => {
                setUser({
                    uuid: response.data.data.uuid,
                    email: response.data.data.email,
                    name_first: response.data.data.name_first,
                    name_last: response.data.data.name_last
                })
                setInitialized(true)
            })
    }, [])

    function advance(target) {
        if (!target) {
            setState(state + 1)
        } else {
            setState(target)
        }
    }

    return (
        <ProgressiveForm
            state={state}
            forms={{
                1: <Password user={user} setUser={setUser} setInitialized={setInitialized} advance={advance}/>,
                2: <TwoFactor user={user} setUser={setUser}/>
            }}
        />
    )
}

export default Login;
