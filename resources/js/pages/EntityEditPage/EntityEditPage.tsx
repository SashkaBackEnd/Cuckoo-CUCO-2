import React from 'react'

import {useHistory, useParams} from 'react-router-dom'
import {Heading} from '@chakra-ui/layout'
import moment from 'moment'

import {EntityForm} from '@components/EntityForm'
import {Loader} from '@components/UI/Loader'
import {Page, PageBody, toast} from '@app/theme'
import {unmaskPhone} from '@app/helpers'
import {IEntityFormValues} from '@models/entity'
import {BackToMain} from '@components/BackToMain'
import {entityAPI} from '@app/services'

export const EntityEditPage: React.FC = () => {
  const {entityId}: never = useParams()
  const history = useHistory()
  const [updateEntity] = entityAPI.useUpdateEntityMutation()
  const {data: entity, isLoading} = entityAPI.useFetchEntityByIdQuery(entityId)

  const submitHandler = async (data: IEntityFormValues) => {
    const normalizedData = {
      ...data,
      phone: unmaskPhone(data.phone),
      servicePhone: unmaskPhone(data.servicePhone),
      callFrom: data.callFrom ? moment(data.callFrom, 'HH:mm').unix() : '',
      callTo: data.callTo ? moment(data.callTo, 'HH:mm').unix() : '',
    }

    await updateEntity(normalizedData)
      .unwrap()
      .then(() => {
        toast({
          title: 'Объект успешно изменен',
        })
        history.push('/entities')
      })
  }

  return (
    <div>
      <BackToMain />
      <Page>
        <PageBody bg="white" p={10}>
          <Heading as="h4" size="lg" mb={6}>
            Редактировать объект
          </Heading>
          {isLoading ? (
            <Loader />
          ) : (
            <EntityForm
              initialValues={{
                ...entity,
                originalId: entity.id,
                centralPostId: entity.centralPost?.id || '',
                callFrom: entity.callFrom ? moment(entity.callFrom * 1000).format('HH:mm') : '',
                callTo: entity.callTo ? moment(entity.callTo * 1000).format('HH:mm') : '',
              }}
              postOptions={entity.posts.map(({id, name}) => ({label: name, value: id}))}
              submitHandler={submitHandler}
            />
          )}
        </PageBody>
      </Page>
    </div>
  )
}
