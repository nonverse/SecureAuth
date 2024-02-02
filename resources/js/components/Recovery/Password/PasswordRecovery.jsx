import {useEffect} from "react";
import RequestPasswordReset from "./RequestPasswordReset";
import ResetPassword from "./ResetPassword";

const PasswordRecovery = ({setInitialized}) => {

    const query = new URLSearchParams(window.location.search)
    const token = query.get('token')

    useEffect(() => {
        setInitialized(true)
    })

    return (
        <>
            {token ? (
                <ResetPassword/>
            ) : (
                <RequestPasswordReset/>
            )}
        </>
    )
}

export default PasswordRecovery;
