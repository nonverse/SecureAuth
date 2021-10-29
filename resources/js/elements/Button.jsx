import React from "react";

const Button = ({label, submit}) => {
    return (
        <button className="button" type={submit ? 'submit' : 'button'}>
            {label}
        </button>
    )
}

export default Button
