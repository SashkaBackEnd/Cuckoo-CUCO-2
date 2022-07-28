import React from 'react'
import { Page, PageBody } from '@app/theme'
import { ReportsListMobile } from '@components/ReportsListMobile/ReportsListMobile'
import { IReportsByManagers } from '@models/reports'

interface IReportsMobilePageProps {
  reports: IReportsByManagers[]
}

export const ReportsMobilePage: React.FC<IReportsMobilePageProps> = ({reports}) => {

  return (
    <Page p={0} pb={7}>
      <PageBody>
        <ReportsListMobile reports={reports} />
      </PageBody>
    </Page>
  )
}

