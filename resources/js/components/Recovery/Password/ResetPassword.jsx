import LinkButton from "../../elements/LinkButton";
import {Formik} from "formik";
import Form from "../../elements/Form";
import Field from "../../elements/Field";
import {useState} from "react";
import validate from "../../../../scripts/validate";
import {useDispatch} from "react-redux";
import {endLoad, startLoad} from "../../../state/load";
import {auth} from "../../../../scripts/api/auth";

const ResetPassword = () => {

    const [error, setError] = useState('')
    const query = new URLSearchParams(window.location.search)
    const dispatch = useDispatch()

    async function submit(values) {

        dispatch(startLoad())

        await auth.post('recovery/password/reset', {
            password: values.password,
            password_confirmation: values.password_confirmation,
            email: query.get('email'),
            token: query.get('token')
        })
            .then((response) => {
                if (response.data.data.success) {
                    dispatch(endLoad())
                    console.log('Done')
                }
            })
            .catch((e) => {
                setError(e.response.data.errors.password)
                dispatch(endLoad())
            })
    }

    function validatePassword(value, compare) {
        setError('')
        return validate.confirmation(value, compare)
    }

    return (
        <>
            <div className="fluid-text">
                <span>Account Recovery</span>
                <h1>Reset your password</h1>
                <LinkButton action={() => {
                    window.location.replace('/login')
                }}>Restart Login</LinkButton>
            </div>
            <Formik initialValues={{}} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors, values}) => (
                    <div>
                        <Form cta={"Continue"}>
                            <Field doesLoad password name={"password"} placeholder={"Set a new password"}
                                   error={errors.password} validate={value => validate.require(value, 8)}/>
                            <Field doesLoad password name={"password_confirmation"}
                                   placeholder={"Confirm your password"}
                                   error={errors.password_confirmation ? errors.password_confirmation : error}
                                   validate={value => validatePassword(value, values.password)}/>
                        </Form>
                    </div>
                )}
            </Formik>
        </>
    )
}

export default ResetPassword;
