import React from "react";
import {Field as FormikField} from "formik";
import LinkButton from "./LinkButton";

const Field = ({password, name, label, placeholder, value, error, validate, change}) => {

    let validator
    if (validate) {
        validator = validate
    } else {
        validator = ''
    }

    return (
        <div className="field-wrapper">
            <span className={"op-05"}>{label}</span>
            <FormikField type={password ? 'password' : 'text'} className={error ? 'field-error' : ''} id={name}
                         name={name} placeholder={placeholder}
                         value={value}
                         validate={validator}/>
            {error ? <span className="error">{error}</span> : ''}
            {change ? (<LinkButton action={change}>Change</LinkButton>) : ''}
        </div>
    )

}
export default Field
