import React, { useMemo, useState } from 'react'
import { SimpleGrid } from '@chakra-ui/react'
import { useHistory } from 'react-router-dom'
import { Loader } from '@components/UI/Loader'
import { BackToMain } from '@components/BackToMain'
import { Page, PageBody } from '@app/theme'
import { ManagersList } from '@components/ManagersList/ManagersList'
import { ManagersSearch } from '@components/ManagersSearch'
import { managerAPI } from '@app/services'


export const ManagersPage: React.FC = (props) => {
  const { children } = props
  const { data, isLoading } = managerAPI.useFetchAllManagersQuery(1)
  const history = useHistory()
  const [filteredManagers, setFilteredManagers] = useState(data)

  const activeManagerId = useMemo(() => {
    const splittedLocation = history.location.pathname.split('/')
    return splittedLocation.length === 3
      ? splittedLocation[splittedLocation.length - 1]
      : undefined
  }, [history.location.pathname])

  const handleSearch = (inputVal: string) => {
    const filt = data?.filter(
      manager => `${manager.patronymic} ${manager.name} ${manager.surname}`.trim().
        toLowerCase().
        includes(inputVal.trim().toLowerCase()))

    setFilteredManagers(filt)
  }

  return (
    <div>
      <BackToMain/>
      <Page>
        <PageBody>
          <SimpleGrid mb={7} columns={[1, 1, 1, 2]} gap={7}>
            <div>
              <ManagersSearch
                handleSearch={handleSearch}
                activeId={activeManagerId}
              />
              {isLoading ? <Loader/> : <ManagersList
                managers={filteredManagers || data}/>}
            </div>
            {children}
          </SimpleGrid>
        </PageBody>
      </Page>
    </div>
  )
}
