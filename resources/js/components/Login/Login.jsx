import {useState} from "react";
import ProgressiveForm from "../elements/ProgressiveForm";
import Password from "./Password";
import TwoFactor from "./TwoFactor";

const Login = ({user, setUser}) => {

    const [state, setState] = useState(0)

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
                0: <Password user={user} setUser={setUser} advance={advance}/>,
                1: <TwoFactor user={user} setUser={setUser}/>
            }}
        />
    )
}

export default Login;
