import React, { useEffect } from 'react'
import { useHistory, useParams } from 'react-router-dom'
import useAxios from 'axios-hooks'
import { IWorker } from '@models/worker'
import {
  IWorkerFormValues,
  WorkerForm,
} from '@components/WorkerForm/WorkerForm'
import { unmaskPhone } from '@app/helpers'
import moment from 'moment'
import axios from 'axios'
import { Page, PageBody, toast } from '@app/theme'
import { errorHandler } from '@app/errors'
import { BackToMain } from '@components/BackToMain'
import { Heading } from '@chakra-ui/layout'
import { Loader } from '@components/UI/Loader'
import { managerAPI } from '@app/services'
import { IManagerFormValues } from '@models/manager'
import { ManagerForm } from '@components/ManagerForm'



export const ManagerEditPage: React.FC = () => {

  const history = useHistory()
  const { managerId }: never = useParams()
  const {
    data: manager,
    error,
    isLoading,
    refetch,
  } = managerAPI.useFetchManagerByIdQuery(managerId)

  const [handleSubmit] = managerAPI.useUpdateManagerMutation()

  const submitHandler = async (manager: IManagerFormValues) => {

    const normalizedData = {
      ...manager,
      email: manager.email,
      phone: unmaskPhone(manager.phone),
    }


      await handleSubmit({ data: normalizedData, manager: normalizedData })
      .unwrap()
      .then(() => {
        toast({
          status: 'success',
          title: 'Работник успешно изменен',
        })
      }).catch(err => {
        errorHandler(err)

        // toast({
        //   status: 'error',
        //   title: 'Ошибка сервера',
        // })
        // errorHandler(err)
      })
    }


  useEffect(() => {
    refetch()
  }, [refetch])

  // @ts-ignore
  return (
    <div>
      <BackToMain/>
      <Page>
        <PageBody bg="white" p={10}>
          <Heading as="h4" size="lg" mb={6}>
            Изменить Менеджера
          </Heading>
          {isLoading || !manager ? (
            <Loader/>
          ) : (
            <ManagerForm
                        initialValues={{ ...manager, oldpassword: '' }}
                         submitHandler={submitHandler}/>
          )}
        </PageBody>
      </Page>
    </div>

  )
}
