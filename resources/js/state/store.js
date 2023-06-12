import { configureStore } from '@reduxjs/toolkit'
import fluidLoadReducer from "./load.js"

export default configureStore({
    reducer: {
        loader: fluidLoadReducer
    },
})
