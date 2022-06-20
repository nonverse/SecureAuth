import {useState} from "react";
import ProgressiveForm from "../elements/ProgressiveForm";
import Password from "./Password";
import TwoFactor from "./TwoFactor";

const Login = ({user, setUser, setInitialized}) => {

    const [state, setState] = useState(1)

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
