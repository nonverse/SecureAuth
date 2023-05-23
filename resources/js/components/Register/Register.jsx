import {useState} from "react";
import Agreement from "./Agreement";
import Details from "./Details";

const Register = () => {

    const [state, setState] = useState(1)

    const views = {
        0: <Agreement advance={advance}/>,
        1: <Details advance={advance}/>
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
