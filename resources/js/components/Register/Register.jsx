import {useEffect, useState} from "react";
import Agreement from "./Agreement";
import {useNavigate} from "react-router-dom";
import {useSelector} from "react-redux";

const Register = () => {

    const [state, setState] = useState(0)
    const user = useSelector(state => state.user.value)
    const navigate = useNavigate()

    const views = {
        0: <Agreement/>
    }

    function advance() {
        setState(state + 1)
    }

    return (
        <>
            {views[state]}
        </>
    )
}

export default Register
