import { Box, Td, Text, Tr } from '@chakra-ui/react'
import { DividerWithText } from '@components/DividerWithText/DividerWithText'
import { EventCard } from '@components/EventList/EventCard'
import React from 'react'
import {
  DividerWithTextTable
} from '@components/DividerWithText/DividerWithTextTable'
import tableClasses from './table.module.css'

interface  IEventTableProps {
  date: string,
  eventsObject: any

}

export const EventTable: React.FC<IEventTableProps> = ({date, eventsObject}) => {

  return (
    <>

      <Tr >
        <Td className={tableClasses.divider_with_text}  p={0} colSpan={6} >
          <DividerWithTextTable> {date} </DividerWithTextTable>
        </Td>
      </Tr>
      {eventsObject[date].map(it =>  <EventCard key={it.id} event={it}/>)}

    </>
  )
}
