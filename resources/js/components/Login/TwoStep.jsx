import Fluid from "../Fluid";
import {useDispatch, useSelector} from "react-redux";
import {useEffect, useState} from "react";
import {updateLoader} from "../../state/loader";
import {Field, Formik} from "formik";
import Form from "../../elements/Form";
import InLineButton from "../../elements/InLineButton";
import DigitInput from "../../elements/DigitInput";
import {auth} from "../../scripts/api/auth";

const TwoStep = () => {

    const user = useSelector(state => state.user.value)
    const [error, setError] = useState('')
    const dispatch = useDispatch()

    useEffect(() => {
        dispatch(updateLoader(false))
    })

    function changeFocus(e) {
        if (e.target.value.length >= e.target.getAttribute("maxlength")) {
            e.target.nextElementSibling.focus();
        }
    }

    return (
        <Fluid heading="Two Step Login" subHeading={`${user.name_first} ${user.name_last}`}>
            <Formik initialValues={{
                digit_1: '',
                digit_2: '',
                digit_3: '',
                digit_4: '',
                digit_5: '',
                digit_6: '',
            }} onSubmit={async (values) => {
                dispatch(updateLoader(true))
                await auth.post('login/two-factor', {
                    authentication_token: user.authentication_token,
                    one_time_password: `${values.digit_1}${values.digit_2}${values.digit_3}${values.digit_4}${values.digit_5}${values.digit_6}`
                })
                    .then(response => {
                        if (response.data.data.complete) {
                            return window.location.replace(process.env.REACT_APP_ACCOUNT_APP)
                        }
                    })
                    .catch(e => {
                        switch (e.response.status) {
                            case 401:
                                setError('Code is incorrect')
                                break
                            default:
                                setError('Something went wrong')
                        }
                    })
            }}>
                {({handleSubmit}) => (
                    <Form id="fluid-form" noSubmit>
                        <DigitInput label="Code" error={error}>
                            <Field name="digit_1" maxLength="1" onInput={(e) => {
                                changeFocus(e)
                            }}/>
                            <Field name="digit_2" maxLength="1" onInput={(e) => {
                                changeFocus(e)
                            }}/>
                            <Field name="digit_3" maxLength="1" onInput={(e) => {
                                changeFocus(e)
                            }}/>
                            <Field name="digit_4" maxLength="1" onInput={(e) => {
                                changeFocus(e)
                            }}/>
                            <Field name="digit_5" maxLength="1" onInput={(e) => {
                                changeFocus(e)
                            }}/>
                            <Field name="digit_6" maxLength="1" onInput={() => {
                                handleSubmit()
                            }}/>
                        </DigitInput>
                        <div className="fluid-actions">
                            <InLineButton id="reset-two-step">Lost authenticator?</InLineButton>
                            <InLineButton id="reset-two-step">Restart login</InLineButton>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default TwoStep
