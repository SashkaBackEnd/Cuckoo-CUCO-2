import React from 'react'
import { IReportsByManagers } from '@models/reports'
import { Avatar, Box, Container, Flex, HStack } from '@chakra-ui/react'
import { Icons } from '@components/UI/iconComponents'
import {
  ReportsListMobileModal,
} from '@components/ReportsListMobile/ReportsListMobileModal'


interface IReportsLIstMobileItemsProps {
  report: IReportsByManagers
}


export const ReportsLIstMobileItems: React.FC<IReportsLIstMobileItemsProps> = ({ report }) => {
  const {
    name,
    caseMissed,
    totalCalls,
    shifts,
    caseShiftChange,
    caseShiftTimeExceed,
    caseShirtError,
    totalErrors,
    caseObjectGuardMismatch,
    totalEmergencyCases,
    totalDoneShifts,
    id,
    startTime,
    endTime,
    totalWorkHoursString,
    totalWorkHours,
    salary,
    object_name,
    fullName,
    shortName,

  } = report

  return (

    <Flex bg={'#FFFFFF'} w={'full'} h={'60px'} pr={'25px'} pl={'16px'}
          spacing={'32px'} justifyContent={'space-between'}>
      <HStack>
        <Avatar h={'28px'} w={'28px'} name={name}/>
        <Box fontSize={'12px'} fontWeight={700}> {name} </Box>
      </HStack>

      <HStack>

        <HStack spacing={'25px'}>

          <HStack>
            <Icons.IconKeyGreen/>
            <p> {totalCalls} </p>

          </HStack>

          <HStack>
            <Icons.IconKeyRed/>
            <p> {totalErrors} </p>

          </HStack>

        </HStack>

        <ReportsListMobileModal report={report}/>
      </HStack>

    </Flex>
  )
}

