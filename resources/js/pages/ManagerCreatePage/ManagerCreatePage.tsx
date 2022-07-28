import React from 'react'
import { Heading } from '@chakra-ui/layout'
import { Page, PageBody, toast } from '../../theme'
import { ManagerForm } from '@components/ManagerForm'
import { BackToEmployees } from '@components/BackToEmployees'
import { managerAPI } from '@app/services'
import { useHistory } from 'react-router-dom'
import { IManagerFormValues } from '@models/manager'
import { unmaskPhone } from '@app/helpers'
import { errorHandler } from '@app/errors'


export const ManagerCreatePage: React.FC = () => {
  const history = useHistory()
  const [createManager] = managerAPI.useCreateManagerMutation()

  const submitHandler = async (data: IManagerFormValues) => {
    const normalizedData = {
      ...data,
      email: data.email,
      phone: unmaskPhone(data.phone),
    }

    await createManager(normalizedData).unwrap().then(() => {
      history.push('/managers')
    }).catch(err => {
      errorHandler(err)
      // toast({
      //   status: 'error',
      //   title: 'Ошибка сервера',
      // })
    })

  }

  return (
    <div>
      <BackToEmployees/>
      <Page>
        <PageBody bg="white" p={10}>
          <Heading as="h4" size="lg" mb={6}>
            Добавить менеджера
          </Heading>
          <ManagerForm submitHandler={submitHandler}/>
        </PageBody>
      </Page>
    </div>
  )
}
