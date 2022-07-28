import {Loader} from '@components/UI/Loader'
import {BackToMain} from '@components/BackToMain'
import {Card, Page, PageBody} from '@app/theme'
import {SimpleGrid} from '@chakra-ui/react'
import React from 'react'
import {AccessSettings} from '@components/AccessSettings/AccessSettings'
import { MobileSetings } from '@components/AccessSettings/MobileSetings'
import { useWindowSize } from '@hooks/useWindowSize'

export const AccessSettingsPage: React.FC = ({children}) => {
    const {isMobile} = useWindowSize()
  const loading = false
  return loading ? (
    <Loader />
  ) : (
    <div>
      <BackToMain />
      <Page>
        <PageBody>
          <SimpleGrid mb={7} columns={1} gap={7}>

          {
              !isMobile ? ( <AccessSettings /> ):( <MobileSetings/>)
          }

            {children}
          </SimpleGrid>
        </PageBody>
      </Page>
    </div>
  )
}
