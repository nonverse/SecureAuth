import {createSlice} from "@reduxjs/toolkit";

export const loaderSlice = createSlice({
    name: "loader",
    initialState: {},
    reducers: {
        updateLoader: (state, action) => {
            state.value = action.payload
        }
    }
})

export const {updateLoader} = loaderSlice.actions
export default loaderSlice.reducer
