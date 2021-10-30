import React, {useState} from "react";
import ProgressiveForm from "../ProgressiveForm";

import Email from "./Email";
import Name from "./Name";
import Username from "./Username";
import Password from "./Password";

const RegisterForm = ({load}) => {

    const [user, updateUser] = useState({
        email: '',
        username: '',
        firstname: '',
        lastname: '',
    })
    const [state, setState] = useState(1)

    function advance() {
        setState(state + 1)
    }

    function previous() {
        setState(state - 1)
    }

    return (
        <ProgressiveForm state={state} views={{
            1: <Email load={load} user={user} updateUser={updateUser} advance={advance}/>,
            2: <Name load={load} user={user} updateUser={updateUser} advance={advance} back={previous}/>,
            3: <Username load={load} user={user} updateUser={updateUser} advance={advance} back={previous}/>,
            4: <Password load={load} user={user} updateUser={updateUser} advance={advance} back={previous}/>
        }}/>
    )
}

export default RegisterForm