import React, {useEffect, useState} from "react";
import ProgressiveForm from "../ProgressiveForm";
import ResetPassword from "./ResetPassword";

const ResetPasswordForm = ({load, setInitialised}) => {

    const [userData, updateUser] = useState({})
    const [state, setState] = useState(1)

    function advance() {
        setState(state + 1)
    }

    useEffect(() => {
        setInitialised(true)
    })

    return (<ProgressiveForm state={state} views={{
        1: <ResetPassword load={load} updateUser={updateUser} advance={advance}/>
        }}/>
    )
}

export default ResetPasswordForm
