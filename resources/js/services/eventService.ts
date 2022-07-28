import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/dist/query/react'


import { baseUrl, prepareHeaders } from '@app/services/configureQuery'
import { IFilteredQueryBody } from '@models/events'




const type = 'Event'

export const eventApI = createApi({
  reducerPath: 'eventApI',
  baseQuery: fetchBaseQuery({
    baseUrl,
    prepareHeaders,
  }),
  refetchOnReconnect: true,
  tagTypes: [type],
  endpoints: (build) => ({
    fetchAllEvents: build.query<any, number>({
      query: () => ({
        url: `/events/list`,
        method: 'GET',
      }),
      providesTags: (result) =>
      result ? [{type, id: 'LIST'}, ...result.map(({id}) => ({type, id} as const))] : [{type, id: 'LIST'}],

    }),



    fetchFilteredEvents: build.query<any, IFilteredQueryBody>({
      query: (body) => ({
        url: `/events/`,
        body: body
      }),
      providesTags: (result) =>
        result ? [{type, id: 'LIST'}, ...result.map(({id}) => ({type, id} as const))] : [{type, id: 'LIST'}],

    }),
    // fetchAllFilteredEvents: build.query<null, number>({
    //   query: () => ({
    //     url: `/events/list`,
    //     method: 'GET',
    //   }),
    //   providesTags: (result) =>
    //     result ? [{type, id: 'LIST'}, ...result.map(({id}) => ({type, id} as const))] : [{type, id: 'LIST'}],
    //
    // }),
    // fetchLogLastDay: build.query<IManager, string>({



  }),
})
