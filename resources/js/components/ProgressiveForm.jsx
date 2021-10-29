import React, {useState} from "react";

const ProgressiveForm = ({load, views}) => {

    const [state, setState] = useState(1)

    function next() {
        setState(state + 1)
    }

    function previous() {
        setState(state - 1)
    }

    let currentForm = 1
    currentForm = views[state];

    return (currentForm)

}

export default ProgressiveForm
