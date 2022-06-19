import {useState} from "react";
import ProgressiveForm from "../elements/ProgressiveForm";
import Name from "./Name";
import Username from "./Username";
import Confirm from "./Confirm";

const Register = ({user, setUser}) => {

    const [state, setState] = useState(1)

    function advance(target) {
        if (!target) {
            setState(state + 1)
        } else {
            setState(target)
        }
    }

    return (
        <ProgressiveForm
            state={state}
            forms={{
                1: <Name user={user} setUser={setUser} advance={advance}/>,
                2: <Username user={user} setUser={setUser} advance={advance}/>,
                3: <Confirm user={user} setUser={setUser} advance={advance}/>,
            }}
        />
    )

}

export default Register;
