import React from 'react'
import { Card } from '@app/theme'
import { IReportsByManagers } from '@models/reports'
import {
  ReportsLIstMobileItems,
} from '@components/ReportsListMobile/ReportsListMobileItems'
import {
  Avatar,
  Box,
  Container,
  Flex,
  HStack,
  Icon,
  VStack,
} from '@chakra-ui/react'
import { Icons } from '@components/UI/iconComponents'
import {
  ReportsListMobileModal,
} from '@components/ReportsListMobile/ReportsListMobileModal'


interface IReportsListMobileProps {
  reports: IReportsByManagers[]
}


export const ReportsListMobile: React.FC<IReportsListMobileProps> = ({ reports }) => {
  return (
    <VStack spacing={'8px'}>
      {reports?.map(report => (
        <ReportsLIstMobileItems key={report.id} report={report}/>
      ))}

    </VStack>
  )
}

