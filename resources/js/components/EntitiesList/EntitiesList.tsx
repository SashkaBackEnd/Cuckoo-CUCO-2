import React from 'react'

import { EntityCard } from './EntityCard/EntityCard'
import classes from './EntitiesList.module.css'
import { IEntity } from '@models/entity'


interface IEntitiesListProps {
  entities: IEntity[]
}


export const EntitiesList: React.FC<IEntitiesListProps> = ({ entities = [] }) => {
  return (
    <div className={classes.List}>
      {entities.map((entity) => (
        <EntityCard key={entity.id} entity={entity}/>
      ))}
    </div>
  )
}
