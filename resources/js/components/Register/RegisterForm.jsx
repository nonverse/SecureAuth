import React, {useEffect, useState} from "react";
import ProgressiveForm from "../ProgressiveForm";

import Email from "./Email";
import Name from "./Name";
import Username from "./Username";
import Password from "./Password";
import VerifyEmail from "./VerifyEmail";
import Activated from "./Activated";

const RegisterForm = ({load, setInitialised}) => {

    const [user, updateUser] = useState({})
    const [state, setState] = useState(1)

    useEffect(() => {
        setInitialised(true)
    }, [])

    function advance() {
        setState(state + 1)
    }

    function previous() {
        setState(state - 1)
    }

    return (
        <>
            <ProgressiveForm state={state} views={{
                1: <Email load={load} userData={user} updateUser={updateUser} advance={advance}/>,
                2: <VerifyEmail load={load} userData={user} updateUser={updateUser} advance={advance}/>,
                3: <Activated load={load} userData={user} advance={advance}/>,
                4: <Name load={load} user={user} updateUser={updateUser} advance={advance} back={previous}/>,
                5: <Username load={load} user={user} updateUser={updateUser} advance={advance} back={previous}/>,
                6: <Password load={load} userData={user} updateUser={updateUser} advance={advance} back={previous}/>
            }}/>
        </>
    )
}

export default RegisterForm
