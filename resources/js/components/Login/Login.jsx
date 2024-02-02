import {useEffect, useState} from "react";
import TwoStep from "./TwoStep";
import Password from "./Password";
import AccountSelector from "./AccountSelector";
import {useDispatch} from "react-redux";
import {useNavigate} from "react-router-dom";
import {updateUser} from "../../state/user";

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
