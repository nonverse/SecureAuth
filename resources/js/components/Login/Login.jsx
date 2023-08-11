import {useEffect, useState} from "react";
import TwoStep from "./TwoStep";
import Password from "./Password";
import AccountSelector from "./AccountSelector";
import {auth} from "../../scripts/api/auth";
import {updateUser} from "../../state/user";
import {useDispatch} from "react-redux";
import {useNavigate} from "react-router-dom";

const Login = () => {

    const [state, setState] = useState(0)
    const query = new URLSearchParams(window.location.search)
    const dispatch = useDispatch()
    const navigate = useNavigate()

    const views = {
        0: <Password advance={advance}/>,
        1: <TwoStep/>,
        2: <AccountSelector restart={restart}/>
    }

    useEffect(() => {
        async function initialise() {
            //TODO Fix visual bug where previous user's login form is displayed momentarily before new user's
            if (query.get('email')) {
                await auth.post('/user/initialize', {
                    email: query.get('email')
                })
                    .then(response => {
                        dispatch(updateUser({
                            ...response.data.data,
                            email: query.get('email')
                        }))
                        navigate(`/login?${new URLSearchParams(window.location.search)}`)
                    })
            }
        }

        initialise()
    })

    function restart() {
        setState(0)
    }

    function advance(to = 0) {
        if (to) {
            setState(to)
        } else {
            setState(state + 1)
        }
    }

    return (
        <>
            {views[state]}
        </>
    )
}

export default Login
