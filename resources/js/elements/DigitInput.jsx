const DigitInput = ({label, children}) => {

    return (
        <>
            <div className="digit-input-label">{label}</div>
            <div className="digit-input">
                {children}
            </div>
        </>
    )
}

export default DigitInput
