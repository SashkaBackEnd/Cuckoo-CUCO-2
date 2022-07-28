import React from 'react'
import { Text } from '@chakra-ui/layout'
import { Box } from '@chakra-ui/react'
import { ManagerCard } from './ManagerCard/ManagerCard'
import { Card } from '@app/theme'
import { IManager } from '@models/manager'


interface IManagersListProps {
  managers: IManager[]
  activeId?: string
}


export const ManagersList: React.FC<IManagersListProps> = (props) => {
  const { managers = [], activeId } = props

  if (!managers.length) {


    return (
      <Card>
        <Text>Менеджера нет</Text>
      </Card>
    )
  }



  return (
    <Box>
      <Text fontSize="xs" mb={3}>
        {managers.length} менеджер
      </Text>
      {managers.map((manager) => (

        <ManagerCard access={undefined} isActive={activeId === manager?.id}
                     key={manager?.id} {...manager} />
      ))}
    </Box>
  )
}
