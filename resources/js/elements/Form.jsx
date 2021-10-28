import React from "react";

const Form = ({title, children}) => {

    return (
        <div className="form">
            <h3>{title}</h3>
            {children}
        </div>
    )
}

export default Form
