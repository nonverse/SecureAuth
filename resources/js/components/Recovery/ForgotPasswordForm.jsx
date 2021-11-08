import React, {useEffect, useState} from "react";
import ProgressiveForm from "../ProgressiveForm";
import ForgotPassword from "./ForgotPassword";

const ForgotPasswordForm = ({load, setInitialised}) => {

    const [state, setState] = useState(1)

    function advance() {
        setState(state + 1)
    }

    useEffect(() => {
        setInitialised(true)
    })

    return (<ProgressiveForm state={state} views={{
            1: <ForgotPassword load={load}/>
        }}/>
    )
}

export default ForgotPasswordForm
