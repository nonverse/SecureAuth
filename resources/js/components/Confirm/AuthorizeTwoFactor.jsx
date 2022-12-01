import {useState} from "react";
import validate from "../../../scripts/validate";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import LinkButton from "../elements/LinkButton";
import FormInformation from "../elements/FormInformation";
import {auth} from "../../../scripts/api/auth";
import {useDispatch} from "react-redux";
import {endLoad, startLoad} from "../../state/load";
import dictionary from "../../../scripts/dictionary";

const AuthorizeTwoFactor = ({user, baseUrl, redirectUrl, invalid, setInitialized}) => {

    const [error, setError] = useState('')
    const [showInfo, setShowInfo] = useState(false)
    const query = new URLSearchParams(window.location.search)
    const dispatch = useDispatch()

    async function submit(values) {

        dispatch(startLoad())

        await auth.post('confirm/two-factor', {
            one_time_password: values.one_time_password,
            authentication_token: user.authentication_token
        })
            .then((response) => {
                if (response.data.data.complete) {
                    dispatch(endLoad())
                    setInitialized(false)

                    let {confirmation_token, token_expiry, token_authenticates} = response.data.data

                    window.location.replace(redirectUrl(confirmation_token,token_expiry, token_authenticates))
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
                <span>Hello, <span className="op-05">{user.name_first} {user.name_last}</span></span>
                <h1>Authorize an action</h1>
                <LinkButton action={() => {
                    window.location.replace(baseUrl)
                }}>Back to app</LinkButton>
            </div>
            <Formik initialValues={{
                one_time_password: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <div className={invalid ? 'op-05 action-cover' : ''}>
                        <Form>
                            <Field doesLoad name={"one_time_password"} placeholder={"Enter Your 2FA Code"}
                                   error={errors.one_time_password ? errors.one_time_password : error}
                                   validate={validateCode}/>
                        </Form>
                        <LinkButton action={() => {
                            setShowInfo(true)
                        }}>Why do I need to authorize?</LinkButton>
                    </div>
                )}
            </Formik>
            {invalid ?
                (
                    <FormInformation weight={'danger'}>
                        Invalid authorization request, please return to app
                    </FormInformation>
                ) : (
                    <FormInformation weight={'default'}>
                        Authorization requested for: <span
                        className="splash">{dictionary.actionByKey(query.get('action_id'))}</span>
                    </FormInformation>
                )}
            {showInfo ?
                (
                    <FormInformation weight={'warning'} close={() => {
                        setShowInfo(false)
                    }}>
                        Some actions require manual authorization so we can verify that it's really you who is
                        executing them.
                    </FormInformation>
                ) : ''}
        </>
    )
}

export default AuthorizeTwoFactor
