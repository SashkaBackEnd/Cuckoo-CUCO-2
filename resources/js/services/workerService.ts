import {createApi, fetchBaseQuery} from '@reduxjs/toolkit/dist/query/react'
import {IEntity, IEntityFormValues} from '@models/entity'
import {baseUrl, prepareHeaders} from '@app/services/configureQuery'
import { IWorker } from '@models/worker'
import { IWorkerFormValues } from '@components/WorkerForm/WorkerForm'


const type = 'Workers'

export const workersAPI = createApi({
  reducerPath: 'workersAPI',
  baseQuery: fetchBaseQuery({
    baseUrl,
    prepareHeaders,
  }),
  refetchOnReconnect: true,
  tagTypes: [ type],
  endpoints: (build) => ({
    fetchAllWorkers: build.query<IWorker[], number>({
      query: () => ({
        url: `/workers`,
      }),
     providesTags: result => [type]
    }),
    fetchWorkerById: build.query<IWorker, string>({
      query: (id) => ({
        url: `/workers/${id}`,
      }),
      providesTags: (result, error, id) => [{type, id}],
    }),
    createWorker: build.mutation<IWorker, IWorkerFormValues>({
      query: (worker) => ({
        url: `/workers`,
        method: 'POST',
        body: worker,
      }),
      invalidatesTags: [type],
    }),
    updateWorker: build.mutation<IEntity, IEntityFormValues>({
      query: (worker) => ({
        url: `/entities/${worker.id}`,
        method: 'PUT',
        body: worker,
      }),
      invalidatesTags: (result, error, {id}) => [{type, id}],
    }),
    deleteWorker: build.mutation<void, string>({
      query: (id) => ({
        url: `/workers/${id}`,
        method: 'DELETE',
      }),
      invalidatesTags: [type],
    }),

  }),
})
