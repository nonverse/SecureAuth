import {useEffect, useState} from "react";
import ProgressiveForm from "../elements/ProgressiveForm";
import {useSelector} from "react-redux";
import ConfirmPassword from "./ConfirmPassword";
import ConfirmTwoFactor from "./ConfirmTwoFactor";

const Confirm = ({user, setUser, setInitialized}) => {

    const [state, setState] = useState(1)
    const load = useSelector((state) => state.loader.value)

    useEffect(() => {
        setInitialized(true)
    })

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
                    1: <ConfirmPassword user={user} setUser={setUser} setInitialized={setInitialized} advance={advance}/>,
                    2: <ConfirmTwoFactor user={user} setUser={setUser} setInitialized={setInitialized} advance={advance}/>
                }}
            />
        </div>
    )
}

export default Confirm;
