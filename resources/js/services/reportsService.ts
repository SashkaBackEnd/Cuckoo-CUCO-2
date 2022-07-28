import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/dist/query/react'
import { baseUrl, prepareHeaders } from '@app/services/configureQuery'
import { IReportsArgs, IReportsByManagers } from '@models/reports'


const type = 'Reports'

export const reportAPI = createApi({
  reducerPath: 'reportAPI',
  baseQuery: fetchBaseQuery({
    baseUrl,
    prepareHeaders,
  }),
  refetchOnReconnect: true,
  tagTypes: [type],
  endpoints: (build) => ({
    fetchAllReportsByManagers: build.query<IReportsByManagers[], IReportsArgs>({
      query: ({ fromDate, toDate, type }) => ({
        url: `/reports/${type}/${fromDate}/${toDate}`,
      }),
      // invalidatesTags:  [type],
      providesTags: (result) =>
        result
          ? [...result.map(({ id }) => ({ type, id } as const)), type]
          : [{ type, id: 'LIST' }],
    }),

  }),
})
