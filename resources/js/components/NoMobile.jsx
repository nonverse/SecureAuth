import React from "react";

const NoMobile = () => {

    return (
        <div className="page-block no-mobile">
            <div className="page-block">
                <div className="error">
                    <div className="error-stack">
                        <span className="default">Unsupported Device!</span>
                        <span className="default" id="under-construction">Please visit this app on a desktop browser</span>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default NoMobile
