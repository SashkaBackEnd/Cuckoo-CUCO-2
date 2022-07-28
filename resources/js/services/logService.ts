import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/dist/query/react'
import {
  IAttachManagerBody,
  IDeactivateManagerBody,
  IManager,
  IManagerFormValues,
} from '@models/manager'
import { baseUrl, prepareHeaders } from '@app/services/configureQuery'
import { ILog } from '@models/post'
import { ILogs } from '@models/log'



const type = 'Log'

export const logApI = createApi({
  reducerPath: 'logApI',
  baseQuery: fetchBaseQuery({
    baseUrl,
    prepareHeaders,
  }),
  refetchOnReconnect: true,
  tagTypes: [type],
  endpoints: (build) => ({
    fetchAllLogs: build.query<ILogs[], number>({
      query: () => ({
        url: `/log`,
      }),
      providesTags: (result) =>
      result ? [{type, id: 'LIST'}, ...result.map(({id}) => ({type, id} as const))] : [{type, id: 'LIST'}],

    }),
    fetchLogLastDay: build.query<ILogs[], number>({
      query: (offset) => ({
        url: `/log/offset/${offset}`,
        method: 'GET',
      }),
      providesTags: (result, error, id) => [{type, id}],
    }),

  }),
})
