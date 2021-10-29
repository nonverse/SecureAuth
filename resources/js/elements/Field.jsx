import React from "react";

const Field = ({name, value, update, password}) => {

    return (
        <div>
            <input type={password ? 'password' : 'text'} placeholder={name} value={value} onChange={(e) => {
                update(e.target.value)
            }}/>
        </div>
    )
}

export default Field
