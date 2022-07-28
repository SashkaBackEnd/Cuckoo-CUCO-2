import React, { useCallback, useMemo } from 'react'

import { useHistory, useParams } from 'react-router-dom'
import { Heading } from '@chakra-ui/layout'
import moment from 'moment'

import { Page, PageBody, toast } from '@app/theme'
import { IPostFormValues } from '@models/post'
import { PostForm, unixToDatesIntervals } from '@components/PostForm/PostForm'
import { BackToMain } from '@components/BackToMain'
import { Loader } from '@components/UI/Loader'

import { IInterval } from '@components/IntervalInput/IntervalInput'
import { entityAPI } from '@app/services'
import { normalizeData } from '@app/helpers/normalizedData'


export const PostEditPage: React.FC = () => {
  const {entityId, postId}: never = useParams()
  const history = useHistory()
  const {data: post, isLoading} = entityAPI.useFetchPostByIdQuery(postId)
  const [updatePost] = entityAPI.useUpdatePostMutation()

  const initialValues = useMemo(() => {
    if (post) {
      const normalizeValues = {
        name: post.name,
        phone: post.phone,
        Mon: {salary: 0, times: []},
        Tue: {salary: 0, times: []},
        Wed: {salary: 0, times: []},
        Thu: {salary: 0, times: []},
        Fri: {salary: 0, times: []},
        Sat: {salary: 0, times: []},
        Sun: {salary: 0, times: []},
        nonStandardWork: [],
      }
      post.standardWork.length &&
        post.standardWork.forEach((work) => {
          normalizeValues[work.day] = {salary: !!work.salary ? work.salary : undefined, times: unixToDatesIntervals(work.hours)}
        })
      post.nonStandardWork.length &&
        post.nonStandardWork.forEach((work) => {
          normalizeValues.nonStandardWork = [
            ...normalizeValues.nonStandardWork,
            {
              id: work.id,
              day: moment(work.day).format('YYYY-MM-DD'),
              salary: !!work.salary ? work.salary : undefined,
              times: unixToDatesIntervals(work.hours),
            },
          ]
        })
      return normalizeValues
    } else {
      return undefined
    }
  }, [post])

  const submitHandler = useCallback(
    async (data: IPostFormValues<IInterval>) => {
      const normalizedData = normalizeData(data)
      

      await updatePost({post: normalizedData, postId, entityId})
        .unwrap()
        .then(() => {
          toast({
            title: 'Пост успешно изменен',
          })
          history.push(`/entities/${entityId}/${postId}`)
        })
    },
    [updatePost, postId, entityId, history]
  )

  return (
    <div>
      <BackToMain />
      <Page>
        <PageBody bg="white" p={10}>
          <Heading as="h4" size="lg" mb={6}>
            Редактировать пост
          </Heading>
          {isLoading ? <Loader /> : <PostForm initialValues={initialValues} submitHandler={submitHandler} />}
        </PageBody>
      </Page>
    </div>
  )
}
