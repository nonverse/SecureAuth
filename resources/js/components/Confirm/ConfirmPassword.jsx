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

const ConfirmPassword = ({user, setUser, baseUrl, redirectUrl, invalid, setInitialized, advance}) => {

    const [error, setError] = useState('')
    const [showInfo, setShowInfo] = useState(false)
    const query = new URLSearchParams(window.location.search)
    const dispatch = useDispatch()

    async function submit(values) {

        dispatch(startLoad())

        await auth.post('confirm', {
            password: values.password,
            authenticates: query.get('authenticates')
        })
            .then((response) => {
                if (response.data.data.complete) {
                    dispatch(endLoad())
                    setInitialized(false)

                    let {confirmation_token, token_expiry, token_authenticates} = response.data.data

                    window.location.replace(redirectUrl(confirmation_token,token_expiry, token_authenticates))
                } else if (response.data.data.authentication_token) {
                    setUser({
                        ...user,
                        authentication_token: response.data.data.authentication_token
                    })
                    dispatch(endLoad())
                    advance()
                }
            })
            .catch((e) => {
                switch (e.response.status) {
                    case 401:
                        setError('Password is incorrect')
                        break;
                    default:
                        setError('Something went wrong')
                }
                dispatch(endLoad())
            })
    }

    function validatePassword(value) {
        setError('')
        return validate.require(value)
    }

    return (
        <>
            <div className="fluid-text">
                <span>Hello, <span className="op-05">{user.name_first} {user.name_last}</span></span>
                <h1>Authenticate an action</h1>
                <LinkButton action={() => {
                    window.location.replace(baseUrl)
                }}>Back to app</LinkButton>
            </div>
            <Formik initialValues={{
                password: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <div className={invalid ? 'op-05 action-cover' : ''}>
                        <Form>
                            <Field doesLoad password name={"password"} placeholder={"Enter Your Password"}
                                   error={errors.password ? errors.password : error} validate={validatePassword}/>
                        </Form>
                        <LinkButton action={() => {
                            setShowInfo(true)
                        }}>Why do I need to authenticate?</LinkButton>
                    </div>
                )}
            </Formik>
            {invalid ?
                (
                    <FormInformation weight={'danger'}>
                        Invalid authentication request, please return to app
                    </FormInformation>
                ) : (
                    <FormInformation weight={'default'}>
                        Authentication requested for: <span
                        className="splash">{dictionary.actionByKey(query.get('authenticates'))}</span>
                    </FormInformation>
                )}
            {showInfo ?
                (
                    <FormInformation weight={'warning'} close={() => {
                        setShowInfo(false)
                    }}>
                        Some actions require manual authentication so we can verify that it's really you who is
                        executing them.
                    </FormInformation>
                ) : ''}
        </>
    )
}

export default ConfirmPassword;
