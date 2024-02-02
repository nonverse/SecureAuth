import {useState} from "react";
import Password from "./Password";

const Confirm = () => {
    const [state, setState] = useState(0)

    const views = {
        0: <Password/>,
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

export default Confirm
