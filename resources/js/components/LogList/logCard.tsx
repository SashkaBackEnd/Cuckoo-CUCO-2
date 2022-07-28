import React, {useState} from 'react'
import {HStack, Td, Tr, Text} from '@chakra-ui/react'
import {IEvents} from '@models/events'
import {ISecurityGuard} from '@models/events'
import {log} from '@models/post'
import {Icons} from '@components/UI/iconComponents'
import {getLogColor} from '@app/helpers'
import moment from 'moment'
import { ILogs } from '@models/log'

interface ILogsListProps {
  log: ILogs,

}

export const LogCard: React.FC<ILogsListProps> = ({ log}) => {

  return (
    <>
      <Tr key={log.id}>
        <Td p={'14px 0px'} fontWeight={400}
            color={'#2C2C2C'}>{log.email}</Td>
        <Td p={'14px 0px'} fontWeight={400}
            color={'#2C2C2C'}>{log.text}</Td>
        <Td p={'14px 0px'} fontWeight={400}
            color={'#2C2C2C'} isNumeric>{moment(log.date * 1000).
          format('D MMMM HH:mm')}</Td>
      </Tr>
  </>
)
}
