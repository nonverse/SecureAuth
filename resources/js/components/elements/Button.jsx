const Button = ({submit, children}) => (

    <button className={"button"} type={submit ? 'submit' : 'button'}>
        {children}
    </button>
)

export default Button;
