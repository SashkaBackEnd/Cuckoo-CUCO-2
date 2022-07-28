import React, { useEffect, useState } from 'react'
import { BackToMain } from '@components/BackToMain'
import { Loader } from '@components/UI/Loader/Loader'
import { Page, PageBody } from '@app/theme'
import {
     SimpleGrid,
    } from '@chakra-ui/react'
import { eventApI } from '@app/services'
import { EventList } from '@components/EventList'



export const EventData = () => {
    const {data: events, isLoading, refetch} = eventApI.useFetchAllEventsQuery(1)


    useEffect(()=> {
      refetch()
  },[refetch])


  return  (
    <div>
      <BackToMain/>
      <Page>
        <PageBody>
          <SimpleGrid mb={7} columns={1} gap={7}>
           <EventList isLoading={isLoading} events={events}/>
          </SimpleGrid>
        </PageBody>
      </Page>
    </div>
  )
}

