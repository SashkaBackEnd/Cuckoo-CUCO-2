import React from 'react'
import { Heading } from '@chakra-ui/layout'
import moment from 'moment'
import { useHistory } from 'react-router-dom'
import { WorkerForm } from '../../components/WorkerForm'
import { BackToMain } from '@components/BackToMain'
import { IWorkerFormValues } from '../../components/WorkerForm/WorkerForm'
import { Page, PageBody, toast } from '../../theme'
import { errorHandler } from '../../errors'
import { unmaskPhone } from '../../helpers'
import { workersAPI } from '@app/services/workerService'



export const WorkerCreatePage: React.FC = () => {
  const history = useHistory()
  const [createWorker, {}] = workersAPI.useCreateWorkerMutation()

  const submitHandler = async (data: IWorkerFormValues) => {
    const normalizedData = {
      ...data,
      phone: unmaskPhone(data.phone),
      birthDate: moment(data.birthDate).unix(),
      licenseToDate: data.licenseToDate
        ? moment(data.licenseToDate).unix()
        : '',
    }
    // @ts-ignore
    await createWorker(normalizedData).then(() => {
      toast({
        title: 'Работник успешно создан',
      })
      history.push(`/workers`)
    }).catch(()=> {
      toast({
        status: 'error',
        title: 'Исправьте ошибки',
      })
    })

  }
  

  return (
    <div>
      <BackToMain/>
      <Page>
        <PageBody bg="white" p={10}>
          <Heading as="h4" size="lg" mb={6}>
            Добавить работника
          </Heading>
          <WorkerForm submitHandler={submitHandler}/>
        </PageBody>
      </Page>
    </div>
  )
}
