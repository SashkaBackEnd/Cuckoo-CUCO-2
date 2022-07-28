import React, { useEffect } from 'react'
import { useParams } from 'react-router-dom'
import { Loader } from '@components/UI/Loader'
import { ManagerInfo } from '@components/ManagerInfo'
import { managerAPI } from '@app/services'


export const ManagerPage: React.FC = () => {
  const { managerId }: never = useParams()
   const { data: manager,  isLoading, refetch } = managerAPI.useFetchManagerByIdQuery(managerId)

  useEffect(() => {
    refetch()
  }, [refetch, managerId])

  return (isLoading && !manager) || !manager ? <Loader/> : <ManagerInfo manager={manager}/>
}
