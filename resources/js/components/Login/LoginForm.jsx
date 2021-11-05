import React, {useState} from "react";
import Email from "./Email";
import Password from "./Password";
import ProgressiveForm from "../ProgressiveForm";
import TwoFactorCheckpoint from "./TwoFactorCheckpoint";

const LoginForm = ({load}) => {

    const [user, updateUser] = useState({})
    const [state, setState] = useState(1)

    function advance() {
        setState(state + 1)
    }

    function previous() {
        setState(state - 1)
    }

    return (
        <ProgressiveForm state={state} views={{
            1: <Email load={load} updateUser={updateUser} advance={advance}/>,
            2: <Password load={load} user={user} updateUser={updateUser} advance={advance} back={previous}/>,
            3: <TwoFactorCheckpoint load={load} user={user}/>
        }}/>
    )
}

export default LoginForm
