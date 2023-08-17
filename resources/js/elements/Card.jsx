const Card = ({name, value, info, noDisplayName, onClick}) => {

    return (
        <div id={`service-${name}`} className="card" onClick={() => {
            if (onClick) {
                onClick()
            }
        }}>
            <div className="card-main">
                {noDisplayName ? '' : <span className="default card-name">{name}</span>}
                <span className="default card-value">{value}</span>
            </div>
            <span className="default card-info">{info}</span>
        </div>
    )
}

export default Card
