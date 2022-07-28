import React, { useState } from 'react'

import {Header} from '@components/Header'
import {EntitiesList} from '@components/EntitiesList'
import {Loader} from '@components/UI/Loader'
import {EntitySearch} from '@components/EntitySearch'
import {Page, PageBody} from '@app/theme'
import {entityAPI, pollingInterval} from '@app/services'

export const EntitiesPage: React.FC = () => {
  const {data: entities, isLoading} = entityAPI.useFetchAllEntitiesQuery(1, {pollingInterval})
  const [entitiesFiltered, setEntitiesFiltered] = useState(entities)

  const handleSearch = (inputVal: string) => {
    const filt = entities?.filter(entity => {
     return  entity.name.toLowerCase().includes(inputVal.trim().toLowerCase())
    })

    setEntitiesFiltered(filt)
  }




  return (
    <div>
      <Header />
      <Page>
        <PageBody p={0}>
          <EntitySearch handleSearch={handleSearch} />
          {isLoading ? <Loader /> : <EntitiesList entities={entitiesFiltered || entities} />}
        </PageBody>
      </Page>
    </div>
  )
}
