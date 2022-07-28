import React, { useEffect } from 'react'

import { Heading } from '@chakra-ui/layout'
import moment from 'moment'
import { useHistory, useParams } from 'react-router-dom'
import axios from 'axios'
import useAxios from 'axios-hooks'

import { WorkerForm } from '@components/WorkerForm'
import { BackToMain } from '@components/BackToMain'
import { IWorkerFormValues } from '@components/WorkerForm/WorkerForm'
import { Page, PageBody, toast } from '@app/theme'
import { errorHandler } from '@app/errors'
import { Loader } from '@components/UI/Loader'
import { IWorker } from '@models/worker'
import { unmaskPhone } from '@app/helpers'


export const WorkerEditPage: React.FC = () => {
  const history = useHistory()
  const { workerId }: never = useParams()
  const [{ data, loading }, reFetch] = useAxios<IWorker>(
    { url: `/api/workers/${workerId}` })

  const submitHandler = async (data: IWorkerFormValues) => {

    const normalizedData = {
      ...data,
      phone: unmaskPhone(data.phone),
      birthDate: moment(data.birthDate+1000).unix(),
      licenseToDate: data.license && data.licenseToDate ? moment(
        data.licenseToDate).unix() : '',
      licenseRank: data.license ? data.licenseRank : '',
    }

    await axios.put(`/api/workers/${workerId}`, normalizedData).then(() => {
      toast({
        status: 'success',
        title: 'Работник успешно изменен',
      })
      history.push(`/workers`)
    }).catch((error) => {
      errorHandler(error)
    })
  }

  useEffect(() => {
    reFetch()
  }, [reFetch])

  // @ts-ignore
  return (
    <div>
      <BackToMain/>
      <Page>
        <PageBody bg="white" p={10}>
          <Heading as="h4" size="lg" mb={6}>
            Изменить работника
          </Heading>
          {loading || !data ? (
            <Loader/>
          ) : (
            <WorkerForm
              initialValues={{
                ...data,
                birthDate: moment.unix(data.birthDate).format('YYYY-MM-DD'),
                licenseToDate: data.licenseToDate ? moment(
                  data.licenseToDate * 1000).format('YYYY-MM-DD') : '',
              }}
              // @ts-ignore
              submitHandler={submitHandler}
            />
          )}
        </PageBody>
      </Page>
    </div>
  )
}
