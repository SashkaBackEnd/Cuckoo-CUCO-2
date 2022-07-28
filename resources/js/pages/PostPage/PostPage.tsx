import React from 'react'
import { useParams } from 'react-router-dom'
import { PostInfo } from '@components/PostInfo'
import { Loader } from '@components/UI/Loader'
import { entityAPI, pollingInterval } from '@app/services'


export const PostPage: React.FC = () => {
  const { postId }: never = useParams()
  const { data: post, isLoading } = entityAPI.useFetchPostByIdQuery(postId)


  const { entityId: objectId }: never = useParams()
  const { data: entity } = entityAPI.useFetchEntityByIdQuery(objectId, {pollingInterval: 3})

  return isLoading ? <Loader/> : <PostInfo posts={entity.posts} post={post}/>
}
