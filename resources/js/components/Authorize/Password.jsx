import Fluid from "../Fluid";
import {Formik} from "formik";
import Form from "../../elements/Form";
import Field from "../../elements/Field";
import validate from "../../scripts/validate";

const Password = () => {

    return (
        <Fluid heading="Authorization Required" subHeading="Update E-Mail">
            <Formik initialValues={{
                password: ''
            }} onSubmit={(values) => {

            }}>
                {({errors}) => (
                    <Form id="fluid-form" cta="Continue">
                        <Field password name="password" label="Password" validate={validate.require} error={errors.password}/>
                        <div className="fluid-text">
                            <p>
                                For your security, we need to verify that it's really you who is making this change to your account
                            </p>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default Password
