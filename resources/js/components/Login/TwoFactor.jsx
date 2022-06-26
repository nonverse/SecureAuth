import LinkButton from "../elements/LinkButton";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import {useState} from "react";
import {useDispatch} from "react-redux";
import {endLoad, startLoad} from "../../state/load";
import {auth} from "../../../scripts/api/auth";
import {useNavigate} from "react-router-dom";

const TwoFactor = ({user, setUser, setInitialized, intended}) => {

    const [error, setError] = useState('')
    const dispatch = useDispatch()
    const navigate = useNavigate()

    async function submit(values) {
        dispatch(startLoad())

        await auth.post('login/two-factor', {
            one_time_password: values.code,
            authentication_token: user.authentication_token
        })
            .then((response) => {
                if (response.data.data.complete) {
                    dispatch(endLoad())
                    setInitialized(false)
                    window.location.replace(`http://${intended.host}${intended.resource}`)
                }
            })
            .catch((e) => {
                switch (e.response.status) {
                    case 422:
                        setError('Something went wrong')
                        break
                    case 500:
                        setError('Something went wrong')
                        break
                    default:
                        setError(e.response.data)
                }
                dispatch(endLoad())
            })
    }

    function validateCode(value) {
        setError('')
        return validate.require(value, 6, 6)
    }

    return (
        <>
            <div className="fluid-text">
                <span>Two Factor Authentication</span>
                <h1>Your account is protected</h1>
                <LinkButton action={() => {
                    window.location.reload()
                }}>Restart Login</LinkButton>
            </div>
            <Formik initialValues={{
                code: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <div>
                        <Form>
                            <Field doesLoad name={"code"} placeholder={"2FA Code"} error={errors.code ? errors.code : error}
                                   validate={validateCode}/>
                        </Form>
                        <LinkButton action={() => {
                            navigate('/recovery/two-factor')
                        }}>Can't access authenticator</LinkButton>
                    </div>
                )}
            </Formik>
        </>
    )

}

export default TwoFactor;
