import React from 'react'

import { useHistory, useParams } from 'react-router-dom'
import { Heading } from '@chakra-ui/layout'

import { BackToMain } from '@components/BackToMain'
import { PostForm } from '@components/PostForm'

import { Page, PageBody, toast } from '@app/theme'

import { IPostFormValues } from '@models/post'
import { entityAPI } from '@app/services'
import { IInterval } from '@components/IntervalInput/IntervalInput'

import { normalizeData } from '@app/helpers/normalizedData'


export const PostCreatePage: React.FC = () => {
  const { entityId }: never = useParams()
  const history = useHistory()
  const [createPost, { error }] = entityAPI.useCreatePostMutation()

  const submitHandler = async (data: IPostFormValues<IInterval>) => {
    const normalizedData = normalizeData(data)

    await createPost({ post: normalizedData, entityId }).unwrap().then((res) => {
      console.log(res, "res in then")
      toast({
        title: 'Пост успешно добавлен',
      })
      history.push(`/entities/${entityId}`)
    }).catch((e) => {
      console.log(e, 'error in post add')

      toast({
        status: 'error',
        title: 'Пост с таким номером телефона уже существует',
      })
    })
  }

  return (
    <div>
      <BackToMain />
      <Page>
        <PageBody bg="white" p={10}>
          <Heading as="h4" size="lg" mb={6}>
            Добавить пост
          </Heading>
          <PostForm submitHandler={submitHandler} />
        </PageBody>
      </Page>
    </div>
  )
}
