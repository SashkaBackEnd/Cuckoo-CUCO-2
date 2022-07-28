

import { combineReducers, configureStore } from '@reduxjs/toolkit'
import { entityAPI, errorLogger, managerAPI,logApI, eventApI } from '@app/services'
import { workersAPI } from '@app/services/workerService'
import { reportAPI } from '@app/services/reportsService'


const rootReducer = combineReducers({
  [entityAPI.reducerPath]: entityAPI.reducer,
  [workersAPI.reducerPath]: workersAPI.reducer,
  [managerAPI.reducerPath]: managerAPI.reducer,
  [logApI.reducerPath]: logApI.reducer,
  [eventApI.reducerPath]: eventApI.reducer,
  [reportAPI.reducerPath]: reportAPI.reducer,
})

// eslint-disable-next-line @typescript-eslint/explicit-module-boundary-types
export const setupStore = () => {
  return configureStore({
    reducer: rootReducer,
    middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(errorLogger, entityAPI.middleware, workersAPI.middleware, managerAPI.middleware, logApI.middleware, eventApI.middleware)
  })
}

export type RootState = ReturnType<typeof rootReducer>
export type AppStore = ReturnType<typeof setupStore>
export type AppDispatch = AppStore['dispatch']
