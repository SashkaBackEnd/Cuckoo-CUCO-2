import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/dist/query/react'
import {
  IAttachManagerBody,
  IDeactivateManagerBody,
  IManager,
  IManagerFormValues,
} from '@models/manager'
import { baseUrl, prepareHeaders } from '@app/services/configureQuery'
import { IAccessObj } from '@components/AccessSettings/ManagerInAccess'


const type = 'Managers'

export const managerAPI = createApi({
  reducerPath: 'managerAPI',
  baseQuery: fetchBaseQuery({
    baseUrl,
    prepareHeaders,
  }),
  refetchOnReconnect: true,
  tagTypes: [type],
  endpoints: (build) => ({
    fetchAllManagers: build.query<IManager[], number>({
      query: () => ({
        url: `/users/managers/list`,
      }),
      providesTags: (result) =>
      result ? [{type, id: 'LIST'}, ...result.map(({id}) => ({type, id} as const))] : [{type, id: 'LIST'}],

    }),
    fetchManagerById: build.query<IManager, string>({
      query: (id) => ({
        url: `/users/${id}`,
      }),
      providesTags: (result, error, id) => [{type, id}],
    }),
    createManager: build.mutation<IManager, IManagerFormValues>({
      query: (manager) => ({
        url: `/createManager`,
        method: 'POST',
        body: manager,
      }),
      invalidatesTags: [type],
    }),
    updateManager: build.mutation<IManager, any>({
      query: (body) => ({
        url: `/users/${body.manager?.id}`,
        method: 'PUT',
        body: body.data,
      }),
      invalidatesTags: (result, error, {id}) => [{type, id}],
    }),
    updateManagerPermissions: build.mutation<IManager, IManager>({
      query: (manager) => ({
        url: `/users/permission/${manager.id}`,
        method: 'POST',
        body: manager.access,
      }),
      invalidatesTags: (result, error, {id}) => [{type, id}],
    }),
    attachManager: build.mutation<IAttachManagerBody, IAttachManagerBody>({
      query: (body) => ({
        url: `/manager/entities/`,
        method: 'POST',
        body: body
      }),
      invalidatesTags: (result, error, user_id) => [{type, user_id}],
    }),
    unAttachManager: build.mutation<IAttachManagerBody, IAttachManagerBody>({
      query: (body) => ({
        url: `/manager/entities/delete`,
        method: 'POST',
        body: body
      }),
      invalidatesTags: (result, error, user_id) => [{type, user_id}],
    }),
    deleteManager: build.mutation<void, string>({
      query: (id) => ({
        url: `/users/${id}`,
        method: 'DELETE',
      }),
      invalidatesTags: [{type, id: 'LIST'}],
    }),
    // deactivateManager: build.mutation<void, string> ({
    //   query: (id) => ({
    //     url: `/deactivate/${id}`,
    //     method: 'POST',

    //   }),
    //   invalidatesTags: [{type, id: 'LIST'}],
    // })
    deactivateManager: build.mutation<IDeactivateManagerBody, IDeactivateManagerBody> ({
      query: (body) => ({
        url: `users/deactivate/${body.user_id}`,
        method: 'POST',
        body: body
      }),
      invalidatesTags: [type],
    }),
    activateManager: build.mutation<IDeactivateManagerBody, IDeactivateManagerBody> ({
      query: (body) => ({
        url: `users/activate/${body.user_id}`,
        method: 'POST',
        body: body
      }),
      invalidatesTags: [type],
    })
  }),
})
