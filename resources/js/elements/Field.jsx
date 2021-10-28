import React from "react";

const Field = ({name, value, update}) => {

    return (
        <div>
            <input type="text" placeholder={name} value={value} onChange={(e) => {
                update(e.target.value)
            }}/>
        </div>
    )
}

export default Field
