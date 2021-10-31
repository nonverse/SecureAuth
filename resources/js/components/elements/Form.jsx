import React from "react";
import {Form as FormikForm} from "formik";

import Button from "./Button";

const Form = ({submitCta, children}) => {

    let cta
    if (submitCta) {
        cta = submitCta;
    } else {
        cta = "Continue"
    }

    return (
        <div className="form-wrapper">
            <FormikForm>
                {children}
                <div className="button-wrapper">
                    <Button label={cta} submit/>
                </div>
            </FormikForm>
        </div>
    )
}

export default Form
