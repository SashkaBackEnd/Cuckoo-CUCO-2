import React, { useMemo, useState } from 'react'

import { SimpleGrid } from '@chakra-ui/react'
import { useHistory } from 'react-router-dom'
import { Loader } from '@components/UI/Loader'
import { BackToMain } from '@components/BackToMain'
import { WorkersSearch } from '@components/WorkersSearch'
import { WorkersList } from '@components/WorkersList'
import { Page, PageBody } from '@app/theme'
import { workersAPI } from '@app/services/workerService'


export const WorkersPage: React.FC = (props) => {
  const { children } = props

  const {
    data,
    isLoading: loading,
    refetch,
  } = workersAPI.useFetchAllWorkersQuery(1)
  const [workersData, setWorkersData] = useState(data)

  const handleSearch = (inputVal: string) => {

    console.log(inputVal, "inputVAl")
    console.log(workersData, "workers")

    const filt = data?.filter(worker =>`${worker.name} ${worker.surname} ${worker.patronymic}`.toLowerCase().
      includes(inputVal.toLowerCase().trim()))

    setWorkersData(filt)
  }

  const history = useHistory()
  const activeWorkerId = useMemo(() => {
    const splittedLocation = history.location.pathname.split('/')
    return splittedLocation.length === 3
      ? splittedLocation[splittedLocation.length - 1]
      : undefined
  }, [history.location.pathname])

  return (
    <div>
      <BackToMain/>
      <Page>
        <PageBody>
          <SimpleGrid mb={7} columns={[1, 2]} gap={7}>
            <div>
              <WorkersSearch handleSearch={handleSearch} refetch={refetch}/>
              {loading ? <Loader/> : <WorkersList  activeId={activeWorkerId}
                                                  workers={ workersData || data}/>}
            </div>
            {children}
          </SimpleGrid>
        </PageBody>
      </Page>
    </div>
  )
}
