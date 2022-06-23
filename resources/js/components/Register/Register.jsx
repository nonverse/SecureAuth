import {useEffect, useState} from "react";
import ProgressiveForm from "../elements/ProgressiveForm";
import Name from "./Name";
import Username from "./Username";
import Confirm from "./Confirm";
import {useNavigate} from "react-router-dom";
import Activate from "./Activate";
import {useSelector} from "react-redux";

const Register = ({user, setUser, setInitialized}) => {

    const [state, setState] = useState(1)
    const load = useSelector((state) => state.loader.value)
    const navigate = useNavigate()

    useEffect(() => {
        if (!user.email) {
            navigate('/')
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
        <div className={load ? 'form-loading action-cover op-05' : ''}>
            <ProgressiveForm
                state={state}
                forms={{
                    1: <Activate user={user} setUser={setUser} advance={advance}/>,
                    2: <Name user={user} setUser={setUser} advance={advance}/>,
                    3: <Username user={user} setUser={setUser} advance={advance}/>,
                    4: <Confirm user={user} setUser={setUser} advance={advance}/>,
                }}
            />
        </div>
    )

}

export default Register;
