import {useState} from "react";
import TwoStep from "./TwoStep";
import Password from "./Password";
import AccountSelector from "./AccountSelector";

const Login = () => {

    const [state, setState] = useState(0)

    const views = {
        0: <Password advance={advance}/>,
        1: <TwoStep/>,
        2: <AccountSelector/>
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
