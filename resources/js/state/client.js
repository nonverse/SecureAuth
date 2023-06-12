import {createSlice} from "@reduxjs/toolkit";

export const clientSlice = createSlice({
    name: 'client',
    initialState: {},
    reducers: {
        updateClient: (state, action) => {
            state.value = action.payload
        }
    }
})

export const { updateClient } = clientSlice.actions
export default clientSlice.reducer
