import React, { useState } from 'react'
import { HStack, Td, Tr, Text } from '@chakra-ui/react'
import { IEvents } from '@models/events'
import { ISecurityGuard } from '@models/events'
import { log } from '@models/post'
import { Icons } from '@components/UI/iconComponents'
import { getLogColor } from '@app/helpers'
import moment from 'moment'
import { getShortName } from '@app/helpers/getShortName'


interface IEventsListProps {
  event: IEvents,

}


export const EventCard: React.FC<IEventsListProps> = ({ event }) => {
  const { type, securityGuard, manager, guardedObject, entity } = event
  return (
    <>
      <Tr >
        <Td p={'14px 8px'} fontWeight={400} color={'#2C2C2C'}>
          <HStack spacing={3}>
            <Icons.IconCircle h={'12px'} w={'12px'} color={getLogColor(type)}/>
            <Text>{log[type] || type} {securityGuard?.shortName}</Text>
          </HStack>
        </Td>

        <Td p={'14px 24px'} fontWeight={400}
            color={'#2C2C2C'}>{securityGuard?.shortName || '-'}</Td>
        <Td p={'14px 24px'} fontWeight={400} color={'#2C2C2C'}>{entity?.name ||
          '-'}</Td>
        <Td p={'14px 24px'} fontWeight={400}
            color={'#2C2C2C'}>{guardedObject?.name || '-'}</Td>
        <Td p={'14px 24px'} fontWeight={400} color={'#2C2C2C'}>{manager?.map(
          man => <Text p={'7px'}>{getShortName(man.name, man.surname, man.patronymic)}</Text>)}</Td>
        <Td p={'14px 24px'} fontWeight={400} color={'#2C2C2C'} isNumeric>{moment(
          +event.date * 1000).format('D MMMM HH:mm')}</Td>

      </Tr>
    </>
  )
}
