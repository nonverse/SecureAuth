import {Form as FormikForm} from "formik";
import Loader from "../components/Loader";

const Form = ({id, loading, children}) => {

    return (
        <div className="form-wrapper">
            <FormikForm className={`form ${loading ? 'element-disabled' : ''}`} id={id}>
                {children}
                <button type="submit">Done</button>
            </FormikForm>
            {loading ? <Loader/> : <></>}
        </div>
    )
}

export default Form