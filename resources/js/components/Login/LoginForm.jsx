import React, {useState} from "react";
import Email from "./Email";
import Password from "./Password";

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
    } else if (state === 2) {
        view = <Password load={load} user={user} advance={next} back={previous}/>
    }

    return (view)
}

export default LoginForm
