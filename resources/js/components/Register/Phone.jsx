import Fluid from "../Fluid";
import {useDispatch, useSelector} from "react-redux";
import {Formik} from "formik";
import Form from "../../elements/Form";
import Select from "../../elements/Select";
import Field from "../../elements/Field";
import world from "../../scripts/helpers/world";
import validate from "../../scripts/validate";
import InLineButton from "../../elements/InLineButton";
import {updateUser} from "../../state/user";
import helpers from "../../scripts/helpers/helpers";

const Phone = ({advance}) => {

    const user = useSelector(state => state.user.value)
    const dispatch = useDispatch()

    return (
        <Fluid id="register-phone" heading={`Welcome, ${user.name_first}`} subHeading="Phone number">
            <Formik initialValues={{
                phone_country: user.phone ? `${helpers.getObjectItem(world.countries, 'dial_code', user.phone.split('-')[0]).dial_code} ${helpers.getObjectItem(world.countries, 'dial_code', user.phone.split('-')[0]).name}` : '+61 Australia',
                phone: user.phone ? user.phone.split('-')[1] : '',
            }} onSubmit={(values) => {
                // TODO check if phone number is being used with an API call
                dispatch(updateUser({
                    ...user,
                    phone: `${values.phone_country.split(' ')[0]}-${values.phone}`
                }))

                advance()
            }}>
                {({values, errors}) => (
                    <Form id="fluid-form" cta="Continue">
                        <div id="phone-form">
                            <Select name="phone_country" label="Country">
                                {world.countries.map(country => (
                                    <option
                                        key={`phone-country-${country.name}`}>{`${country.dial_code} ${country.name}`}</option>
                                ))}
                            </Select>
                            <Field name="phone" label="Phone" validate={value => validate.require(value, 4, 12)}
                                // TODO Better & complete phone number validation
                                   error={errors.phone ? 'Please enter a valid phone number' : ''}/>
                        </div>
                        <div className="fluid-text">
                            <p>
                                Your phone number will be used to send for receiving priority alerts regarding
                                your account. It will also be useful in the event that you lose access to your
                                account
                            </p>
                        </div>
                        <div className="fluid-actions">
                            <InLineButton id="skip-phone" onClick={() => {
                                advance()
                            }}>Skip for now</InLineButton>
                            <InLineButton onClick={() => {
                                advance(true)
                            }}>Back</InLineButton>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default Phone
