import React, {useEffect, useState} from "react";
import Email from "./Email";
import Password from "./Password";
import ProgressiveForm from "../ProgressiveForm";
import TwoFactorCheckpoint from "./TwoFactorCheckpoint";
import user from "../../scripts/api/user";

const LoginForm = ({load, setInitialised}) => {

    const [userData, updateUser] = useState({})
    const [state, setState] = useState(1)

    function advance() {
        setState(state + 1)
    }

    function previous() {
        setState(state - 1)
    }

    async function getCookie() {
        await user.getCookie()
            .then((response) => {
                let data = response.data.data
                updateUser({
                    email: data.email,
                    name_first: data.name_first,
                    name_last: data.name_last
                })
                setState(2)
                setInitialised(true)
            })
            .catch((e) => {
                setInitialised(true)
            })
    }

    useEffect(() => {
        getCookie()
    }, [])

    return (
        <ProgressiveForm state={state} views={{
            1: <Email load={load} updateUser={updateUser} advance={advance}/>,
            2: <Password load={load} user={userData} updateUser={updateUser} advance={advance} back={previous}/>,
            3: <TwoFactorCheckpoint load={load} user={userData}/>
        }}/>
    )
}

export default LoginForm
