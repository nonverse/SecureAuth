import React, {useEffect, useState} from "react";
import ProgressiveForm from "../ProgressiveForm";
import ForgotPassword from "./ForgotPassword";
import PasswordResetEmailSent from "./PasswordResetEmailSent";

const ForgotPasswordForm = ({load}) => {

    const [userData, updateUser] = useState({})
    const [state, setState] = useState(1)

    function advance() {
        setState(state + 1)
    }

    return (<ProgressiveForm state={state} views={{
            1: <ForgotPassword load={load} advance={advance} updateUser={updateUser}/>,
            2: <PasswordResetEmailSent userData={userData}/>
        }}/>
    )
}

export default ForgotPasswordForm
