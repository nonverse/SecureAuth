import React from "react";
import {Field as FormikField} from "formik";

const Field = ({password, name, placeholder, errors, validate}) => {

    let error
    if (errors) {
        error = errors[name]
    }
    let validator
    if (validate) {
        validator = validate
    } else {
        validator = ''
    }

    return (
        <div className="field-wrapper">
            <FormikField type={password ? 'password' : 'text'} className={error ? 'field-error' : ''} id={name}
                         name={name} placeholder={placeholder}
                         validate={validator}/>
            {error ? <span className="error">{error}</span> : ''}
        </div>
    )

}
export default Field
