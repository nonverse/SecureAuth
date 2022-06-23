import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import {auth} from "../../../scripts/api/auth";

const Confirm = ({user, setUser, advance}) => {

    async function submit(values) {

        await auth.post('register', {
            ...user,
            password: values.password,
            password_confirmation: values.password_confirmation
        })
            .then((response) => {
                // Post registration logic
            })
            .catch(() => {
                // Handle errors
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
                            <Field name={"email_display"} label={"Email"} value={user.email}/>
                        </div>
                        <Field change={() => {
                            advance(2)
                        }} name={"name_display"} label={"Name"} value={`${user.name_first} ${user.name_last}`}/>
                        <Field change={() => {
                            advance(3)
                        }} name={"username_display"} label={"Username"} value={user.username}/>
                        <br/>
                        <Field password doesLoad name={"password"} placeholder={"Set a password"}
                               error={errors.password}
                               validate={value =>
                                   validate.require(value, 8)
                               }/>
                        <Field password doesLoad name={"password_confirmation"} placeholder={"Confirm your password"}
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
