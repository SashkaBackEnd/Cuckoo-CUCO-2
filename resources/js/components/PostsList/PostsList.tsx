import React from 'react'

import {Box, Text} from '@chakra-ui/layout'

import {PostCard} from './PostCard/PostCard'
import {IPost} from '@models/post'
import {Card} from '@app/theme'

interface IPostsListProps {
  posts?: IPost[]
  centralId?: string
  activeId?: string
}

export const PostsList: React.FC<IPostsListProps> = (props) => {
  const {posts = [], centralId, activeId} = props

  if (!posts.length) {
    return (
      <Card>
        <Text>постов нет</Text>
      </Card>
    )
  }

  return (
    <Box>
      <Text fontSize="xs" mb={3}>
        {posts.length} постов
      </Text>
      {posts.map((post) => (
        <PostCard isActive={activeId == post.id} isCentral={centralId === post.id} key={post.id} post={post} />
      ))}
    </Box>
  )
}
