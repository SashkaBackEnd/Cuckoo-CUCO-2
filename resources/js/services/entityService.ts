import {createApi, fetchBaseQuery} from '@reduxjs/toolkit/dist/query/react'
import {IEntity, IEntityFormValues} from '@models/entity'
import {baseUrl, prepareHeaders} from '@app/services/configureQuery'
import {IPost, IPostFormValues} from '@models/post'

const type = 'Entities'

export const entityAPI = createApi({
  reducerPath: 'entityAPI',
  baseQuery: fetchBaseQuery({
    baseUrl,
    prepareHeaders,
  }),
  refetchOnReconnect: true,
  tagTypes: [type, 'Posts'],
  endpoints: (build) => ({
    fetchAllEntities: build.query<IEntity[], number>({
      query: (page = 1) => ({
        url: `/entities?page=${page}`,
      }),
      providesTags: (result) =>
        result ? [{type, id: 'LIST'}, ...result.map(({id}) => ({type, id} as const))] : [{type, id: 'LIST'}],
    }),
    fetchEntityById: build.query<IEntity, string>({
      query: (id) => ({
        url: `/entities/${id}`,
      }),
      providesTags: (result, error, id) => [{type, id}],
    }),
    createEntity: build.mutation<IEntity, IEntityFormValues>({
      query: (entity) => ({
        url: `/entities`,
        method: 'POST',
        body: entity,
      }),
      invalidatesTags: [{type, id: 'LIST'}],
    }),
    updateEntity: build.mutation<IEntity, IEntityFormValues>({
      query: (entity) => ({
        url: `/entities/${entity.originalId}`,
        method: 'PUT',
        body: entity,
      }),
      invalidatesTags: (result, error, {id}) => [{type, id}],
    }),
    deleteEntity: build.mutation<void, string>({
      query: (id) => ({
        url: `/entities/${id}`,
        method: 'DELETE',
      }),
      invalidatesTags: [{type, id: 'LIST'}],
    }),
    toggleDial: build.mutation<void, {id: string; set: boolean}>({
      query: ({id, set}) => ({
        url: `/entities/${id}/set-dialing-status`,
        method: 'PUT',
        body: {set},
      }),
      invalidatesTags: (result, error, {id}) => [{type, id}],
    }),
    fetchPostById: build.query<IPost, string>({
      query: (id) => ({
        url: `/entities/posts/${id}`,
      }),
      providesTags: (result, error, id) => [{type, id}],
    }),
    deletePost: build.mutation<void, {id: string; entityId: string}>({
      query: ({id}) => ({
        url: `/entities/posts/${id}`,
        method: 'DELETE',
      }),
      invalidatesTags: (result, error, {entityId: id}) => [{type, id}],
    }),
    checkPost: build.mutation<void, {id: string; entityId: string}>({
      query: ({id, entityId}) => ({
        url: `entities/${entityId}/posts/${id}/check`,
      }),
      invalidatesTags: (result, error, {id, entityId}) => [
        {type, id: entityId},
        {type: 'Posts', id},
      ],
    }),
    endShift: build.mutation<void, {guardId: string}>({
      query: (body) => ({
        url: `/shifts/end`,
        method: 'POST',
        body: body,
      }),
      invalidatesTags: (result, error, workerId) => [{type, workerId}],
    }),
    createPost: build.mutation<IPost, {post: IPostFormValues; entityId: string}>({
      query: ({post, entityId}) => ({
        url: `/entities/${entityId}/posts`,
        method: 'POST',
        body: post,
      }),
      invalidatesTags: (result, error, {entityId: id}) => [{type, id}],
    }),
    updatePost: build.mutation<
      IPost,
      {
        post: IPostFormValues
        postId: string
        entityId: string
      }
    >({
      query: ({post, postId, entityId}) => ({
        url: `/entities/${entityId}/posts/${postId}`,
        method: 'PUT',
        body: post,
      }),
      invalidatesTags: (result, error, {entityId, postId: id}) => [
        {type, id: entityId},
        {type: 'Posts', id},
      ],
    }),
  }),
})
