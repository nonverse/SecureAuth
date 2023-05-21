import {Field as FormikField} from "formik";

const Select = ({name, label, error, children}) => {

    return (
        <FormikField name={name}>
            {({field: {value}, form: {setFieldValue}}) => (
                <div className="field-wrapper">
                    <span className="field-label">{label}</span>
                    <select id={name} className="field" defaultValue={value} onChange={(e) => {
                        setFieldValue(name, e.target.value)
                    }}>
                        {children}
                    </select>
                    <span className="field-error">{error}</span>
                </div>
            )}
        </FormikField>
    )
}

export default Select