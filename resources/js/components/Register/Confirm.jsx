import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import {auth} from "../../../scripts/api/auth";
import {useDispatch} from "react-redux";
import {endLoad, startLoad} from "../../state/load";
import FormInformation from "../elements/FormInformation";

const Confirm = ({user, setUser, advance}) => {

    const dispatch = useDispatch()

    async function submit(values) {

        dispatch(startLoad())

        await auth.post('register', {
            ...user,
            password: values.password,
            password_confirmation: values.password_confirmation
        })
            .then((response) => {
                dispatch(endLoad())
            })
            .catch(() => {
                // Handle errors
            })
    }

    return (
        <>
            <FormInformation weight={'default'}>
                Your account is almost ready. Please check your details before submitting
            </FormInformation>
            <br/>
            <br/>
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
