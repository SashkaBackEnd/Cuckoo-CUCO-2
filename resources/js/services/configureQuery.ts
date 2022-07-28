import { TOKEN_KEY } from '@app/api'
import { errorHandler } from '@app/errors'
import {
  isRejectedWithValue,
  Middleware,
  MiddlewareAPI,
} from '@reduxjs/toolkit'


export const prepareHeaders = (headers: Headers): Headers => {
  const token = localStorage.getItem(TOKEN_KEY)

  if (token) {
    headers.set('Authorization', `Bearer ${token}`)
  }

  return headers
}

export const baseUrl = '/api'
export const pollingInterval = 30000

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export const errorLogger: Middleware = (_api: MiddlewareAPI) => (next) => (action) => {
  if (isRejectedWithValue(action)) {
    errorHandler(action)
    // errorHandler(action.error)
  }

  return next(action)
}
