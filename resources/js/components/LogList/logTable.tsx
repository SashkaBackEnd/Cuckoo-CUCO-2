import { Td, Tr } from '@chakra-ui/react'
import React from 'react'
import { LogCard } from '@components/LogList/logCard'
import {
  DividerWithTextTable
} from '@components/DividerWithText/DividerWithTextTable'
import tableClasses from '@components/EventTable/table.module.css'


interface  ILogTableProps {
  date: string,
  logsObject: any

}

export const LogTable: React.FC<ILogTableProps> = ({date, logsObject}) => {

  return (
    <>
      <Tr>
        <Td className={tableClasses.divider_with_text}  p={0} colSpan={6}>
          <DividerWithTextTable> {date} </DividerWithTextTable>
        </Td>
      </Tr>
      {logsObject[date]?.map(it =>  <LogCard key={it.id} log={it}/>)}

    </>
  )
}
