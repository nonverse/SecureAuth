import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";

const Confirm = ({user, setUser}) => {

    async function submit(values) {
        setUser({
            ...user,
            password: values.password,
            password_confirmation: values.password_confirmation
        })
    }

    return (
        <>
            <div className="fluid-text">
                <span>Your account is almost ready, please check your <br/>details before submitting</span>
            </div>
            <Formik initialValues={{
                password: '',
                password_confirmation: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors, values}) => (
                    <Form>
                        <div className="op-05 action-cover">
                            <Field name={"email_display"} label={"Email"} value={"isuru2003a@gmail.com"}/>
                        </div>
                        <Field change={() => {
                            //
                        }} name={"name_display"} label={"Name"} value={"Isuru Abhayaratne"}/>
                        <Field change={() => {
                            //
                        }} name={"username_display"} label={"Username"} value={"IsuruA"}/>
                        <br/>
                        <Field password name={"password"} placeholder={"Set a password"} error={errors.password}
                               validate={value =>
                                   validate.require(value, 8)
                               }/>
                        <Field password name={"password_confirmation"} placeholder={"Confirm your password"}
                               error={errors.password_confirmation} validate={value =>
                            validate.confirmation(value, values.password)
                        }/>
                    </Form>
                )}
            </Formik>
        </>
    )

}

export default Confirm;
