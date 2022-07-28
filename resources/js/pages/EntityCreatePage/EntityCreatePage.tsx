import React from 'react'

import {useHistory} from 'react-router-dom'
import {Heading} from '@chakra-ui/layout'
import moment from 'moment'

import {EntityForm} from '@components/EntityForm'
import {Page, PageBody, toast} from '@app/theme'
import {BackToMain} from '@components/BackToMain'
import {entityAPI} from '@app/services'
import {IEntityFormValues} from '@models/entity'
import {unmaskPhone} from '@app/helpers'

export const EntityCreatePage: React.FC = () => {
  const history = useHistory()
  const [createEntity] = entityAPI.useCreateEntityMutation()

  const submitHandler = async (data: IEntityFormValues) => {
    const normalizedData = {
      ...data,
      phone: unmaskPhone(data.phone),
      servicePhone: unmaskPhone(data.servicePhone),
      callFrom: data.callFrom ? moment(data.callFrom, 'HH:mm').unix() : '',
      callTo: data.callTo ? moment(data.callTo, 'HH:mm').unix() : '',
    }

    console.log(normalizedData, "normalized data")

    await createEntity(normalizedData)
      .unwrap()
      .then(() => {
        toast({
          title: 'Объект успешно создан',
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
            Добавить объект
          </Heading>
          <EntityForm submitHandler={submitHandler} />
        </PageBody>
      </Page>
    </div>
  )
}
