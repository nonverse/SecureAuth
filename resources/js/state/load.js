import {createSlice} from "@reduxjs/toolkit";

export const fluidLoadSlice = createSlice({
    name: 'fluid-loader',
    initialState: {
        value: false
    },
    reducers: {
        startLoad: (state) => {
            state.value = true
        },
        endLoad: (state) => {
            state.value = false
        }
    }
})

export const { startLoad, endLoad } = fluidLoadSlice.actions

export default fluidLoadSlice.reducer
