import React from "react";
import Button from "./Button";

const Form = ({title, submit, children}) => {

    return (
        <div className="form">
            <h3>{title}</h3>
            {children}
            <div className="button-wrapper">
                <Button label={"Continue"} onclick={submit}/>
            </div>
        </div>
    )
}

export default Form
