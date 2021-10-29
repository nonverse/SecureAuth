import React from "react";

const Field = ({name, value, update, password}) => {

    return (
        <input type={password ? 'password' : 'text'} placeholder={name} value={value} onChange={(e) => {
            update(e.target.value)
        }}/>
    )
}

export default Field
