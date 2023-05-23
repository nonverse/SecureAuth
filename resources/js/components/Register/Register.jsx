import {useState} from "react";
import Agreement from "./Agreement";
import Details from "./Details";
import Phone from "./Phone";

const Register = () => {

    const [state, setState] = useState(2)

    const views = {
        0: <Agreement advance={advance}/>,
        1: <Details advance={advance}/>,
        2: <Phone advance={advance}/>
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
