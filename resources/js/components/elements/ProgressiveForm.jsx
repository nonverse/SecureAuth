const ProgressiveForm = ({state, forms}) => {

    let form
    if (forms[state]) {
        form = forms[state]
    } else {
        form = "Form not found"
    }

    return (form)
}

export default ProgressiveForm;
