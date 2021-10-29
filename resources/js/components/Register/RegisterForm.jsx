import React, {useState} from "react";
import ProgressiveForm from "../ProgressiveForm";

import Email from "./Email";
import AccountInformation from "./AccountInformation";

const RegisterForm = ({load}) => {

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
            2: <AccountInformation load={load} user={user} updateUser={updateUser} advance={advance} back={previous}/>
        }}/>
    )
}

export default RegisterForm
