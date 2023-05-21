const Fluid = ({heading, subheading, children}) => {

    return (
        <div className="fluid">
            <div className="fluid-heading">
                <h1>{heading}</h1>
                <h2>{subheading}</h2>
            </div>
            <div className="fluid-content">
                {children}
            </div>
        </div>
    )
}

export default Fluid
