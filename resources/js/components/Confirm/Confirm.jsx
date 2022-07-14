import {useEffect, useState} from "react";
import ProgressiveForm from "../elements/ProgressiveForm";
import {useSelector} from "react-redux";
import ConfirmPassword from "./ConfirmPassword";
import ConfirmTwoFactor from "./ConfirmTwoFactor";
import dictionary from "../../../scripts/dictionary";

const Confirm = ({user, setUser, setInitialized}) => {

    const [state, setState] = useState(1)
    const [invalid, setInvalid] = useState(false)
    const load = useSelector((state) => state.loader.value)
    const query = new URLSearchParams(window.location.search)
    const baseUrl = `http://${decodeURIComponent(query.get('host'))}${decodeURIComponent(query.get('resource'))}`

    useEffect(() => {
        if (!dictionary.actionByKey(query.get('authenticates'))) {
            setInvalid(true)
        }
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
                    1: <ConfirmPassword user={user} setUser={setUser} baseUrl={baseUrl} invalid={invalid} setInitialized={setInitialized} advance={advance}/>,
                    2: <ConfirmTwoFactor user={user} setUser={setUser} baseUrl={baseUrl} invalid={invalid} setInitialized={setInitialized} advance={advance}/>
                }}
            />
        </div>
    )
}

export default Confirm;
