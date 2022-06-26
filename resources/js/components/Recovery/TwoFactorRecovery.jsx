import LinkButton from "../elements/LinkButton";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import {useEffect, useState} from "react";
import {useDispatch} from "react-redux";
import {endLoad, startLoad} from "../../state/load";
import {auth} from "../../../scripts/api/auth";
import FormInformation from "../elements/FormInformation";

const TwoFactorRecovery = ({user, setInitialized, intended}) => {

    const [error, setError] = useState('')
    const [showInfo, setShowInfo] = useState(false)
    const dispatch = useDispatch()

    useEffect(() => {
        if (!user.authentication_token) {
            window.location.replace('/login')
        }
    })

    async function submit(values) {
        dispatch(startLoad())

        await auth.post('login/two-factor', {
            totp_recovery_token: values.recovery_token,
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

    function validateToken(value) {
        setError('')
        return validate.require(value)
    }

    return (
        <>
            <div className="fluid-text">
                <span>Account Recovery</span>
                <h1>Two Factor Authentication</h1>
                <LinkButton action={() => {
                    window.location.replace('/login')
                }}>Restart Login</LinkButton>
            </div>
            <Formik initialValues={{
                recovery_token: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <div>
                        <Form>
                            <Field doesLoad name={"recovery_token"} placeholder={"2FA Recovery Token"} error={errors.recovery_token ? errors.recovery_token : error}
                                   validate={validateToken}/>
                        </Form>
                        <LinkButton action={() => {
                            setShowInfo(true)
                        }}>I dont have my recovery token</LinkButton>
                    </div>
                )}
            </Formik>
            <FormInformation weight={'danger'}>
                This process will disable 2FA on your account and will invalidate your current recovery token
            </FormInformation>
            {showInfo ? (
                <FormInformation weight={'warning'} close={() => {
                    setShowInfo(false)
                }}>
                    Your recovery token was emailed to you when you enabled 2FA on your account. If you cannot find your recovery token, please contact <a
                    href="https://www.nonverse.net/support" target={"_blank"} rel={"noreferrer"}>Nonverse Support</a>
                </FormInformation>
            ) : ''}
        </>
    )

}

export default TwoFactorRecovery;
