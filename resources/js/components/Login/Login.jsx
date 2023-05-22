import {useState} from "react";
import TwoStep from "./TwoStep";
import Password from "./Password";

const Login = () => {

    const [state, setState] = useState(0)

    const views = {
        0: <Password advance={advance}/>,
        1: <TwoStep/>
    }

    function advance() {
        setState(state + 1)
    }

    return (
        <>
            {views[state]}
        </>
    )
}

export default Login
