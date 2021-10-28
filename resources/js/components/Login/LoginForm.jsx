import React, {useState} from "react";
import Email from "./Email";

const LoginForm = ({load}) => {
    const [state, advance] = useState(1)
    const [user, updateUser] = useState({})

    function next() {
        advance(state + 1)
    }

    function previous() {
        advance(state - 1)
    }

    let view = false;
    if (state === 1) {
        view = <Email load={load} updateUser={updateUser} advance={next}/>
    }

    return (view)
}

export default LoginForm
