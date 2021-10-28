import React from "react";

const Button = ({label, onclick}) => {
    return (
        <div className="button" onClick={() => {onclick()}}>
            {label}
        </div>
    )
}

export default Button
