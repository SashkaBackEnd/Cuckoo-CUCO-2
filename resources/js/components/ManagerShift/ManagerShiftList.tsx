import React from 'react'

import {
  Avatar,
  Box,
  Text,
  Link as ExternalLink,
  BoxProps,

} from '@chakra-ui/react'
import { Divider, HStack,  } from '@chakra-ui/layout'

import { Icons } from '@components/UI/iconComponents'

import { entityAPI } from '@app/services'

import {  IShiftManagers } from '@models/entity'
import {
  guardedObjectsWordInRussian,
  workersWordInRussian,
} from '@app/helpers/wordList'
import { maskPhone } from '@app/helpers/maskPhone'

interface IManagerShiftProps extends BoxProps {
  entities: IShiftManagers[]
  managerId: string
  workerCount: string
  guardedObjectCount: string

}


interface IShiftItemProps {
  entities: IShiftManagers[]
  managerId?: string
  entityId?: string
  workerCount: string
  guardedObjectCount: string
  item?: IShiftManagers
}


const ShiftItem: React.FC<IShiftItemProps> = (props) => {
  const {
    item,
  } = props

  return (
    <Box px={6} py={5} my={2} border="1px" borderColor="gray.100">
        <>
          <Box key={item.entity.id} mb={4}>
            <HStack mb={3}>
              <Avatar size="sm" name={item.entity.name}/>
              <Text>{item.entity.name}</Text>
            </HStack>
            <Text fontSize="14px" fontWeight="400"
                  color={'#8C8C8C'}>{item.entity.address}</Text>
            <HStack spacing={6} mt={4}>
              <ExternalLink href={`tel:${item.entity.phone}`}>
                <Icons.IconPhone mr={2}/>
                { maskPhone(item.entity.phone) }
              </ExternalLink>
            </HStack>
          </Box>

          <Divider/>
          <HStack>
            <Icons.IconVector/>
              <Text>{item.guarded_objects_count} {guardedObjectsWordInRussian(
              +item.guarded_objects_count)}</Text>
          </HStack>
          <HStack p={'4px 8px'} borderRadius={'4px'} mt={4} bg="#EDEDED"
                  w="135px">
            <Icons.IconWorker/>
            <Text>{item.worker_count} {workersWordInRussian(+item.worker_count)}</Text>
          </HStack>
        </>
    </Box>
  )
}

export const ManagerShift: React.FC<IManagerShiftProps> = (props) => {
  const {
    entities,
    guardedObjectCount,
    workerCount,
    managerId,
    ...rest
  } = props

  if (!entities) {
    return null
  }

  return (
    <>
      <Text fontSize="16px" fontWeight="700">Объекты менеджера</Text>
      <Box {...rest}>
        {entities.map(item =>   <ShiftItem guardedObjectCount={guardedObjectCount}
                                           workerCount={workerCount} managerId={managerId}
                                           entities={entities} item={item}/> )}

      </Box>
    </>
  )
}
