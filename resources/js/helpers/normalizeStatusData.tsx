import React from 'react'
import { Icons } from '@components/UI/iconComponents'
import { HStack, Text } from '@chakra-ui/react'
import { MANAGER_STATUS } from '@app/helpers/constants/managerStatuses'


export const normalizeStatusData = (status: number, isRightAligned: boolean) =>{
  switch(status){
    case MANAGER_STATUS.free:
      return statusElement("Свободен", "#4CC557", isRightAligned)
    case MANAGER_STATUS.deactivated:
      return statusElement("Деактивирован", "red.400", isRightAligned)
    case MANAGER_STATUS.withJob:
      return statusElement("Закреплен к объекту", "#3E74F4", isRightAligned)
    default:
      return statusElement("Нет данных", "black.300", isRightAligned)
  }
}

const statusElement = (text: string, color: string, isRightAligned: boolean): JSX.Element => {
  return <HStack justifyContent={isRightAligned ? "flex-end": "flex-start" } width={'full'}>
    <Icons.IconStop borderRadius='10px' color={color} boxSize='7px'/>
    <Text minWidth={"50px"} color={color} fontSize='12px'> {text} </Text>
  </HStack>
}
