import {Formik} from "formik";
import Select from "../../elements/Select";
import calendar from "../../scripts/helpers/calendar";
import Field from "../../elements/Field";
import helpers from "../../scripts/helpers/helpers";
import {useDispatch, useSelector} from "react-redux";
import Fluid from "../Fluid";
import Form from "../../elements/Form";
import InLineButton from "../../elements/InLineButton";
import {updateUser} from "../../state/user";

const DateOfBirth = ({advance}) => {

    const user = useSelector(state => state.user.value)
    const dispatch = useDispatch()

    function validateDay(value, month, year) {

        let key = helpers.getObjectKey(calendar.months, month)
        const thirtyOne = [1, 3, 5, 7, 8, 10, 12]

        if (++key === 2) {
            if (year % 4 === 0) {
                if (value < 1 || value > 29) {
                    return true
                }
            } else {
                if (value < 1 || value > 28) {
                    return true
                }
            }
        } else if (thirtyOne.includes(++key)) {
            if (value < 1 || value > 30) {
                return true
            }
        } else {
            if (value < 1 || value > 31) {
                return true
            }
        }

    }

    function validateYear(value) {
        if (value.length !== 4) {
            return true
        }
    }

    return (
        <Fluid id="register-birthday" heading={`Welcome, ${user.name_first}`} subHeading="Date of birth">
            <Formik initialValues={{
                dob_month: '',
                dob_day: '',
                dob_year: ''
            }} onSubmit={(values) => {
                let month = helpers.getObjectKey(calendar.months, values.dob_month)
                dispatch(
                    updateUser({
                        ...user,
                        dob: `${values.dob_year}-${++month}-${values.dob_day}`
                    }))

                advance()
            }}>
                {({values, errors}) => (
                    <Form id="fluid-form" cta="Continue">
                        <div id="birthday-form">
                            <Select name="dob_month" label="Month"
                                    error={(errors.dob_day || errors.dob_year) ? 'Please enter a valid date' : false}>
                                {Object.values(calendar.months).map(month => (
                                    <option key={`dob-month-${month}`}>{month}</option>
                                ))}
                            </Select>
                            <Field name="dob_day" label="Day"
                                   validate={value => validateDay(value, values.dob_month, values.dob_year)}
                                   error={errors.dob_day}/>
                            <Field name="dob_year" label="Year" validate={validateYear} error={errors.dob_year}/>
                        </div>
                        <div className="fluid-text">
                            <p>
                                Your date of birth will be used to personalise your experience across Nonverse
                                applications and services. Nonverse may also restrict access to certain content
                                based on the age on your profile
                            </p>
                        </div>
                        <div className="fluid-actions">
                            <InLineButton id="skip-birthday" onClick={() => {
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

export default DateOfBirth
