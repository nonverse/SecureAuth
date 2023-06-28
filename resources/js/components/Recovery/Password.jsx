import RequestPasswordReset from "./RequestPasswordReset";
import {useEffect, useState} from "react";
import validate from "../../scripts/validate";
import MessageBox from "../MessageBox";
import ResetPassword from "./ResetPassword";

const Password = () => {

    const query = new URLSearchParams(window.location.search)
    const [state, setState] = useState(0)
    const views = {
        0: <RequestPasswordReset setState={setState}/>,
        1: <MessageBox id="password-reset-sent">An e-mail with instructions to reset your password has been sent to you</MessageBox>,
        2: <ResetPassword/>
    }

    useEffect(() => {
        if (query.get('token') && !validate.email(query.get('email'))) {
            setState(2)
        }
    }, [])

    return (
        views[state]
    )
}

export default Password
