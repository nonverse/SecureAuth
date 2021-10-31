import React, {useEffect, useState} from 'react';
import ReactDOM from 'react-dom';

import FluidLoader from "./FluidLoader";
import AuthRouter from "../Routers/AuthRouter";
import LogoDark from "./elements/LogoDark";
import api from "../scripts/api/api";
import Loader from "./Loader";

function Index() {

    const [loading, setLoading] = useState(false);
    const [initialised, setInitialised] = useState(false);

    useEffect(async () => {
        await api.initialiseCsrf()
            .then(() => {
                setInitialised(api.initialised)
            })
    }, [])

    return (
        <div className="fluid-container">
            <div className="fluid">
                <LogoDark/>
                <AuthRouter load={setLoading}/>
                {loading ? <FluidLoader/> : ''}
            </div>
            {initialised ? '' : <Loader/>}
        </div>
    );
}

export default Index;

if (document.getElementById('root')) {
    ReactDOM.render(<Index/>, document.getElementById('root'));
}
