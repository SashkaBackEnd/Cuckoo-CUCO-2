import React, { useEffect, useState } from 'react'

import { Text } from '@chakra-ui/layout'
import { Box } from '@chakra-ui/react'

import { WorkerCard } from './WorkerCard/WorkerCard'
import { IWorker } from '@models/worker'
import { Card } from '@app/theme'


interface IWorkersListProps {
  workers: IWorker[]
  activeId?: string
}


export const WorkersList: React.FC<IWorkersListProps> = (props) => {
  const { workers = [], activeId } = props


  if (!workers.length) {
    return (
      <Card>
        <Text>Работников нет</Text>
      </Card>
    )
  }



  return (
    <Box>
      <Text fontSize="xs" mb={3}>
        {workers.length} работников
      </Text>
      {workers.map((worker) => (
        <WorkerCard isActive={activeId === worker.id}
                    key={worker.id} {...worker} />
      ))}
    </Box>
  )
}
