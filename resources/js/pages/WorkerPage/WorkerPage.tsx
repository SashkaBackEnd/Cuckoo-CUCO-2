import React, { useEffect } from 'react'

import { useParams } from 'react-router-dom'
import useAxios from 'axios-hooks'

import { IWorker } from '@models/worker'
import { Loader } from '@components/UI/Loader'
import { WorkerInfo } from '@components/WorkerInfo'
import { entityAPI } from '@app/services'


export const WorkerPage: React.FC = () => {
  const { workerId }: never = useParams()
  const [{ data, loading }, fetch] = useAxios<IWorker>(
    { url: `/api/workers/${workerId}`, method: 'get' })


  useEffect(() => {
    fetch()

  }, [fetch, workerId])

  return (loading && !data) || !data ? <Loader/> : <WorkerInfo worker={data}/>
}
