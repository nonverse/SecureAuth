import {Field as FormikField} from "formik";

const Field = ({password, className, name, label, placeholder, error, validate, maxLength, onInput}) => {

    return (
        <FormikField name={name} validate={validate}>
            {({field: {value}, form: {setFieldValue}}) => (
                <div className={`field-wrapper ${error ? 'has-error' : ''}`}>
                    <span className="field-label">{label}</span>
                    <input id={name} className={`field ${className}`} type={password ? 'password' : 'text'} placeholder={placeholder}
                           defaultValue={value}
                           maxLength={maxLength}
                           onInput={(e) => {
                               if (onInput) {
                                   onInput(e)
                               }
                           }}
                           onChange={(e) => {
                               setFieldValue(name, e.target.value)
                           }}/>
                    <span className="field-error">{error}</span>
                </div>
            )}
        </FormikField>
    )
}

export default Field 