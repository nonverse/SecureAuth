import React, {useEffect, useState} from "react";
import ProgressiveForm from "../ProgressiveForm";
import ResetPassword from "./ResetPassword";
import PasswordResetConfirmation from "./PasswordResetConfirmation";

const ResetPasswordForm = ({load}) => {

    const [state, setState] = useState(1)

    useEffect(() => {
        setInitialised(true)
    }, [])

    function advance() {
        setState(state + 1)
    }

    return (<ProgressiveForm state={state} views={{
            1: <ResetPassword load={load} advance={advance}/>,
            2: <PasswordResetConfirmation/>
        }}/>
    )
}

export default ResetPasswordForm
