import React, { useEffect } from 'react'
import {BackToMain} from '@components/BackToMain'
import {Page, PageBody} from '@app/theme'
import {
  Heading,
  SimpleGrid,
} from '@chakra-ui/react'
import {logApI} from '@app/services'

import { Loader } from '@components/UI/Loader/Loader'
import { LogList } from '@components/LogList'

export const LogDataPage = () => {
  const {data: log, isLoading, refetch} = logApI.useFetchAllLogsQuery(1)
  const {data: logsByDay, isLoading: isLoadingByDays } = logApI.useFetchLogLastDayQuery(3)


  useEffect(()=> {
      refetch()
  },[refetch])

  return isLoading ? (
    <Loader/>
  ) : (
    <div>
      <BackToMain/>
      <Page>
        <PageBody>
          <SimpleGrid mb={7} columns={1} gap={7}>
            <LogList/>
          </SimpleGrid>
        </PageBody>
      </Page>
    </div>
  )
}

