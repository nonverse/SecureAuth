import {useEffect, useState} from "react";
import ProgressiveForm from "../elements/ProgressiveForm";
import Name from "./Name";
import Username from "./Username";
import Confirm from "./Confirm";
import {useNavigate} from "react-router-dom";
import Activate from "./Activate";

const Register = ({user, setUser, setInitialized}) => {

    const [state, setState] = useState(1)
    const query = new URLSearchParams(window.location.search)
    const navigate = useNavigate()

    useEffect(() => {
        if (!user.email) {
            if (query.get('email')) {
                setUser({
                    email: query.get('email')
                })
                setInitialized(true)
            } else {
                navigate('/')
            }
        } else {
            setInitialized(true)
        }
    }, [])

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
                1: <Activate user={user} setUser={setUser} advance={advance}/>,
                2: <Name user={user} setUser={setUser} advance={advance}/>,
                3: <Username user={user} setUser={setUser} advance={advance}/>,
                4: <Confirm user={user} setUser={setUser} advance={advance}/>,
            }}
        />
    )

}

export default Register;
