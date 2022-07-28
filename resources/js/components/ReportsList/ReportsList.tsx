import React from 'react'
import { Card } from '@app/theme'

import { IReportsByManagers } from '@models/reports'
import ReportItem from '@components/ReportsList/ReportItem'
import { Text } from '@chakra-ui/react'


interface IReportsListsProps {
  reports: IReportsByManagers[]
  type?: string | number
}




export const ReportsList: React.FC<IReportsListsProps> = (props) => {
  const { reports, type } = props


  if (!reports?.length) {
    return (
      <Text textAlign={'center'}>
        Нет данных
      </Text>
    )
  }

  return (
    <Card mt={2} bg={'#f7f8f9'} p={0}>
      {  reports?.map(
        (singleReport, idx) => !!singleReport.shifts.length && <ReportItem type={type} key={idx} report={singleReport}/>)}
    </Card>
  )
}

