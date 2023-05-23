import {useState} from "react";
import Agreement from "./Agreement";
import Details from "./Details";
import Phone from "./Phone";
import DateOfBirth from "./DateOfBirth";

const Register = () => {

    const [state, setState] = useState(3)

    const views = {
        0: <Agreement advance={advance}/>,
        1: <Details advance={advance}/>,
        2: <Phone advance={advance}/>,
        3: <DateOfBirth advance={advance}/>,
    }

    function advance(back) {
        setState(state + 1)

        if (back) {
            setState(state - 1)
        }
    }

    return (
        <>
            {views[state]}
        </>
    )
}

export default Register
