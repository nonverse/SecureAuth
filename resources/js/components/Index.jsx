import React, {useState} from 'react';
import ReactDOM from 'react-dom';

import FluidLoader from "./FluidLoader";
import AuthRouter from "../Routers/AuthRouter";
import LogoDark from "../elements/LogoDark";

function Index() {

    const [loading, setLoading] = useState(false);

    return (
        <div className="fluid">
            <LogoDark/>
            <AuthRouter load={setLoading}/>
            {loading ? <FluidLoader/> : ''}
        </div>
    );
}

export default Index;

if (document.getElementById('root')) {
    ReactDOM.render(<Index/>, document.getElementById('root'));
}
