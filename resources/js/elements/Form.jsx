import React from "react";
import Button from "./Button";

const Form = ({title, submit, children}) => {

    return (
        <form className="form" onSubmit={(e) => {
            e.preventDefault()
            submit()
        }}>
            <h3>{title}</h3>
            {children}
            <div className="button-wrapper">
                <Button label={"Continue"} onclick={submit}/>
            </div>
        </form>
    )
}

export default Form
