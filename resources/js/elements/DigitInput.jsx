const DigitInput = ({label, error, children}) => {

    return (
        <div className="digit-input-wrapper">
            <span className="digit-input-label">{label}</span>
            <div className="digit-input">
                {children}
            </div>
            <span className="digit-input-error">{error}</span>
        </div>
    )
}

export default DigitInput
