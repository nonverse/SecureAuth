import {useEffect, useState} from "react";
import ProgressiveForm from "../elements/ProgressiveForm";
import Password from "./Password";
import TwoFactor from "./TwoFactor";
import {auth} from "../../../scripts/api/auth";
import {useSelector} from "react-redux";

const Login = ({user, setUser, setInitialized}) => {

    const [state, setState] = useState(1)
    const load = useSelector((state) => state.loader.value)
    const query = new URLSearchParams(window.location.search)
    const intended = {
        host: query.get('host'),
        resource: query.get('resource')
    }

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
        <div className={load ? 'form-loading action-cover op-05' : ''}>
            <ProgressiveForm
                state={state}
                forms={{
                    1: <Password user={user} setUser={setUser} setInitialized={setInitialized} intended={intended}
                                 advance={advance}/>,
                    2: <TwoFactor user={user} setUser={setUser} setInitialized={setInitialized} intended={intended}/>
                }}
            />
        </div>
    )
}

export default Login;
