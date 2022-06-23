import React from "react";
import {Field as FormikField} from "formik";
import LinkButton from "./LinkButton";
import {BarLoader} from "react-spinners";
import {useSelector} from "react-redux";


const Field = ({password, name, label, placeholder, value, error, validate, change, doesLoad}) => {

    const load = useSelector((state) => state.loader.value)

    let validator
    if (validate) {
        validator = validate
    } else {
        validator = ''
    }

    const css = `
    margin-bottom: 5%;
    `;

    return (
        <div className="field-wrapper">
            <span className={"op-05"}>{label}</span>
            <FormikField type={password ? 'password' : 'text'} className={error ? 'field-error' : ''} id={name}
                         name={name} placeholder={placeholder}
                         value={value}
                         validate={validator}/>
            {doesLoad
                ? <BarLoader color={"#6951FF"} width={"100%"} css={css} height={"3px"} loading={load}/>
                : <div className="fluid-slice"/>
            }
            {error ? <span className="error">{error}</span> : ''}
            {change ? (<LinkButton action={change}>Change</LinkButton>) : ''}
        </div>
    )

}
export default Field
