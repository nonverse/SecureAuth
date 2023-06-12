const InLineButton = ({id, onClick, children}) => {

    return (
        <span id={id} className="button inline-button" onClick={() => {
            onClick()
        }}>{children}</span>
    )
}

export default InLineButton