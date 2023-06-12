import {Form as FormikForm} from "formik";
import Loader from "../components/Loader";

const Form = ({id, loading, cta, noSubmit = false, children}) => {

    return (
        <div className="form-wrapper">
            <FormikForm className={`form ${loading ? 'element-disabled' : ''}`} id={id}>
                {children}
                {noSubmit ? '' : <button type="submit">{cta ? cta : 'Done'}</button>}
            </FormikForm>
            {loading ? <Loader/> : <></>}
        </div>
    )
}

export default Form
