const LinkButton = ({children, action}) => (

    <span className="link link-button" onClick={() => {action()}}>
        {children}
    </span>
)
export default LinkButton;
