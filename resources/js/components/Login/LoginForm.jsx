import React, {useState} from "react";
import Email from "./Email";

const LoginForm = ({load}) => {
    const [state, advance] = useState(1)
    const [credentials, setCredentials] = useState({})

    function next() {
        advance(state + 1)
    }

    function previous() {
        advance(state - 1)
    }

    let view = false;
    if (state === 1) {
        view = <Email load={load} setCredentials={setCredentials} advance={next}/>
    }

    return (view)
}

export default LoginForm
