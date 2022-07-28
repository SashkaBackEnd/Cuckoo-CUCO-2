import React, { useEffect, useMemo } from 'react'

import { useHistory, useParams } from 'react-router-dom'
import { SimpleGrid } from '@chakra-ui/react'

import { BackToMain } from '@components/BackToMain'
import { Loader } from '@components/UI/Loader'
import { EntityInfo } from '@components/EntityInfo'
import { PostsList } from '@components/PostsList'
import { PostsSearch } from '@components/PostsSearch'
import { Page, PageBody } from '@app/theme'
import { entityAPI } from '@app/services'


export const EntityPage: React.FC = (props) => {
  const { children } = props
  const { entityId }: never = useParams()
  const { data: entity, isLoading } = entityAPI.useFetchEntityByIdQuery(
    entityId)
  const history = useHistory()
  const activePostId = useMemo(() => {
    const splittedLocation = history.location.pathname.split('/')
    return splittedLocation.length === 4
      ? splittedLocation[splittedLocation.length - 1]
      : undefined
  }, [history.location.pathname])


  useEffect(() => {
    if (!activePostId && entity && entity.posts.length) {
      history.push(`/entities/${entityId}/${entity.posts[0]?.id}`)
    }
  }, [activePostId, entity, entityId, history])


  return isLoading ? (
    <Loader/>
  ) : (
    <div>
      <BackToMain/>
      <Page>
        <PageBody>
          <EntityInfo entity={entity}/>
          <SimpleGrid mb={7} columns={[1,2]} gap={7}>
            <div>
              <PostsSearch entityId={entityId}/>
              <PostsList activeId={activePostId}
                         centralId={entity?.centralPost?.id}
                         posts={entity.posts}/>
            </div>
            {children}
          </SimpleGrid>
        </PageBody>
      </Page>
    </div>
  )
}
