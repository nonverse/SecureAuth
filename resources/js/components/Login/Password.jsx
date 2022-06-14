import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import LinkButton from "../elements/LinkButton";

const Password = () => {

    async function submit(values) {
        //
    }

    return (
        <>
            <div className="fluid-text">
                <span>Welcome back</span>
                <h1>Isuru Abhayaratne</h1>
                <LinkButton>Not You?</LinkButton>
            </div>
            <Formik initialValues={{
                password: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form>
                        <Field password name={"password"} placeholder={"Enter Your Password"} error={errors.password} validate={validate.require}/>
                    </Form>
                )}
            </Formik>
        </>
    )
}

export default Password;
