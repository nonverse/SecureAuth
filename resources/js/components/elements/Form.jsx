import react from "react";
import {Form as FormikForm} from "formik";
import Button from "./Button";

const Form = ({cta, children}) => {

    if (!cta) {
        cta = 'Submit'
    }

    return (
        <div className="form-wrapper">
            <FormikForm>
                {children}
                <Button submit>{cta}</Button>
            </FormikForm>
        </div>
    )
}

export default Form;
