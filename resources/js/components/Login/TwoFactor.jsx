import LinkButton from "../elements/LinkButton";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";

const TwoFactor = ({user, setUser}) => {

    async function submit(values) {
        //
    }

    return (
        <>
            <div className="fluid-text">
                <span>Two Factor Authentication</span>
                <h1>Your account is protected</h1>
                <LinkButton>Restart Login</LinkButton>
            </div>
            <Formik initialValues={{
                code: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form>
                        <Field password name={"code"} placeholder={"2FA Code"} error={errors.code} validate={validate.otp}/>
                    </Form>
                )}
            </Formik>
        </>
    )

}

export default TwoFactor;
