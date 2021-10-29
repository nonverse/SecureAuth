import React, {useState} from "react";
import Email from "./Email";
import Password from "./Password";
import ProgressiveForm from "../ProgressiveForm";

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
            2: <Password load={load} user={user} advance={advance} back={previous}/>
        }}/>
    )
}

export default LoginForm
