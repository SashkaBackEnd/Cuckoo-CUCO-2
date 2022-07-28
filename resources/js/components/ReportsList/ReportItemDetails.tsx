import React from 'react'
import {
  Avatar, Box,
  HStack,
  Text,
  VStack,
} from '@chakra-ui/react'
import { Icons } from '@components/UI/iconComponents'
import { IReportsByManagers } from '@models/reports'
import { Circle } from '@chakra-ui/layout'
import moment from 'moment'
import { TYPES_FETCH } from '@pages/ReportsPage/ReportsPage'


interface IReportDetailItemProps {
  report: IReportsByManagers
  type?: string | number
}


const ReportItemDetails: React.FC<IReportDetailItemProps> = ({ report, type }) => {
  const {
    caseShiftTimeExceed,
    caseMissed,
    caseShirtError,
    totalErrors,
    totalCalls,
    shifts,
    caseShiftChange,
    totalWorkHours,
    totalEmergencyCases,
    totalDoneShifts,
    id,
    caseObjectGuardMismatch,
    name,
    shortName,
    fullName,
    object_name,
    salary,
    endTime,
    startTime,
    totalWorkHoursString,
  } = report

  return (
    <>


      <Box textAlign="right" mr="1.8rem" >
        <HStack justifyContent={'space-between'} mt={'32px'}>


          <HStack  alignItems={'center'} p={'0'}   >

            {(object_name ||  type === TYPES_FETCH[3] ) && <Circle size="36px" bg="red.300" color="white">
              <Icons.IconShield w={4} h={6}/>
            </Circle>}

            {shortName && <Avatar size="sm" h={'32px'} w={'32px'} name={fullName}/>}

            <VStack alignItems={'flex-start'}>
              {shortName && <Text fontWeight={700}> {shortName} </Text>}

              <Text fontWeight={700}> {object_name ? object_name : name} </Text>
              {object_name && <Text m={0} fontWeight={400} color={'#8C8C8C'}
                                    fontSize={'12px'}> {name} </Text>}
            </VStack>

            <Box ml={'16px'} p={'4px 8px'} borderRadius={'4px'} bg={'#EDEDED'}>
              {startTime && endTime ? <Text m={0}> {moment(startTime * 1000).
                format('DD MMM HH:mm')} - {moment(endTime * 1000).
                format('DD MMM HH:mm')}</Text> : (<Text>Нет данных</Text>)}
            </Box>
          </HStack>

          <HStack p={'6px 16px 5px 16px'} borderRadius={'4px'} bg={'#E4ECFF'}>
            <Text fontWeight={400} color={'#878787'}>
              {totalWorkHoursString}
            </Text>
            <Text fontSize="14px" fontWeight="700">
              {salary} ₽
            </Text>
          </HStack>

        </HStack>
      </Box>

      <HStack pb={'32px'} mt={'23px'} overflowX={'auto'} spacing={'28px'} alignItems={'flex-start'} borderBottom={'1px solid #ECECEC'}>
        <VStack whiteSpace={"nowrap"} minWidth={'87.5px'} alignItems={'flex-start'}>
          <Text fontSize="12px" color={'#8C8C8C'}
                fontWeight={400}>Звонков</Text>
          <HStack  m={0}><Icons.IconPhone h={'16px'} w={'16px'}
                                          color="blue"/><Text>{totalCalls}</Text>
          </HStack>
        </VStack>

        <VStack whiteSpace={"nowrap"}  minWidth={'87.5px'} alignItems={'flex-start'} borderRight={'1px solid #E8E8E8'}>

          <Text fontSize="12px" color={'#8C8C8C'}
                fontWeight={400}>Ошибки</Text>
          <HStack><Icons.IconErrrors h={'16px'} w={'16px'}
                                     color="white"
                                     bg="white"/><Text>{totalErrors}</Text>
          </HStack>
        </VStack>


        <VStack whiteSpace={"nowrap"}  alignItems={'flex-start'}>
          <Text fontSize="12px" color={'#8C8C8C'}
                fontWeight={400}>Ошибки при дозвоне</Text>
          <HStack><Icons.IconEllips
            h={'8px'} w={'8px'}
            color="red"/><Text>{caseMissed}</Text>
          </HStack>
        </VStack>

        <VStack whiteSpace={"nowrap"}   alignItems={'flex-start'}>
          <Text fontSize="12px" color={'#8C8C8C'}
                fontWeight={400}>Ошибок заступления на
            смену</Text>
          <HStack><Icons.IconEllips
            h={'8px'} w={'8px'}
            color="blue"/><Text>{caseShirtError}</Text>
          </HStack>
        </VStack>

        <VStack whiteSpace={"nowrap"}  alignItems={'flex-start'}>
          <Text fontSize="12px" color={'#8C8C8C'}
                fontWeight={400}>Ошибка повторного
            заступления</Text>
          <HStack><Icons.IconEllips
            h={'8px'} w={'8px'}
            color="blue"/><Text>{caseObjectGuardMismatch}</Text>
          </HStack>
        </VStack>

        <VStack whiteSpace={"nowrap"}  alignItems={'flex-start'}>
          <Text fontSize="12px" color={'#8C8C8C'}
                fontWeight={400}>Превышение времени
            смены</Text>
          <HStack><Icons.IconEllips
            h={'8px'} w={'8px'}
            color="blue"/><Text>{caseShiftTimeExceed}</Text>
          </HStack>
        </VStack>


      </HStack>

    </>
  )
}

export default ReportItemDetails
