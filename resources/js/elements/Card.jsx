const Card = ({name, value, noDisplayName, onClick}) => {

    return (
        <div id={`service-${name}`} className="card" onClick={() => {
            if (onClick) {
                onClick()
            }
        }}>
            {noDisplayName ? '' : <span className="default card-name">{name}</span>}
            <span className="default card-value">{value}</span>
        </div>
    )
}

export default Card
