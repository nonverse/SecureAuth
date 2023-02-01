const Button = ({submit, action, children}) => {
    return (
        <button className={"button"} type={submit ? 'submit' : 'button'} onClick={() => {
            if (!submit) {
                action()
            }
        }}>
            {children}
        </button>
    )
}

export default Button;
