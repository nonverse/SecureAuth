import Fluid from "../Fluid";
import {useDispatch, useSelector} from "react-redux";
import {Formik} from "formik";
import Field from "../../elements/Field";
import Form from "../../elements/Form";
import InLineButton from "../../elements/InLineButton";
import {updateUser} from "../../state/user";
import validate from "../../scripts/validate";
import {useState} from "react";
import {auth} from "../../scripts/api/auth";

const Details = ({advance}) => {

    const user = useSelector(state => state.user.value)
    const [error, setError] = useState('')
    const dispatch = useDispatch()

    function validateUsername(value) {
        setError('')
        if (value === user.email) {
            setError('Username cannot be the same as e-mail')
            return true
        }
        if (validate.require(value)) {
            setError(validate.require(value))
            return true
        }
    }

    return (
        <Fluid id="register-details" heading="Welcome" subHeading={user.email}>
            <Formik initialValues={{
                email: user.email,
                username: user.username ? user.username : '',
                name_first: user.name_first ? user.name_first : '',
                name_last: user.name_last ? user.name_last : ''
            }} onSubmit={async (values) => {

                await auth.post('register/validate-username', values)
                    .then(response => {
                        dispatch(
                            updateUser({
                                ...values
                            })
                        )
                        advance()
                    })
                    .catch(e => {
                        switch (e.response.status) {
                            case 422:
                                setError('That username is taken')
                                break
                            default:
                                setError('Something went wrong')
                        }
                    })
            }}>
                {({errors, isSubmitting}) => (
                    <Form loading={isSubmitting} id="fluid-form" cta="Continue">
                        {/*<Field readOnly name="email" label="E-Mail"/>*/}
                        <Field name="username" label="Username" validate={validateUsername}
                               error={error}/>
                        {/*TODO Validate username using API*/}
                        <div id="register-name">
                            <Field name="name_first" label="First Name" validate={validate.require}
                                   error={errors.name_first}/>
                            <Field name="name_last" label="Last Name" validate={validate.require}
                                   error={errors.name_last}/>
                        </div>
                        <div className="fluid-text">
                            <p>
                                Your name is considered to be personal information and is hidden to other users by
                                default,
                                you can choose to change this setting once the account setup process is complete
                            </p>
                        </div>
                        <div className="fluid-actions">
                            <InLineButton id="restart-registration" onClick={() => {
                                window.location.replace('/')
                            }}>Restart registration</InLineButton>
                            <InLineButton id="restart-registration" onClick={() => {
                                window.location.replace('/')
                            }}>Login instead</InLineButton>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default Details
