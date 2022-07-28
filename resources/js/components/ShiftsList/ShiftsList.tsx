import React, { useCallback } from 'react'

import {
  Avatar,
  Box,
  Text,
  Link as ExternalLink,
  BoxProps,
  Button,
  SimpleGrid,
} from '@chakra-ui/react'
import { Divider, Flex, HStack } from '@chakra-ui/layout'
import moment from 'moment'

import { ICurrentShift, IPost, log } from '@models/post'
import { Icons } from '@components/UI/iconComponents'
import Pictogram from '@components/UI/Pictogram/Pictogram'
import { toast } from '@app/theme'
import { TAxiosRefetch } from '@models/axios'
import { getLocaleCurrency } from '@app/helpers'
import { entityAPI } from '@app/services'
import classes from './ShiftsList.module.css'
import { isTimeAgoSeconds } from '@app/helpers/isTimeAgoSeconds'


interface IShiftsListProps extends BoxProps {
  currentShifts: ICurrentShift[]
  refetch?: TAxiosRefetch
  entityId: string
  posts: IPost[]
}


interface IWorkerShiftProps extends BoxProps {
  currentShift: ICurrentShift
  workerId: string
}


interface IShiftItemProps {
  currentShift: ICurrentShift
  workerId?: string
  entityId?: string
  posts?: IPost[]
  post?: IPost
}


const ShiftItem: React.FC<IShiftItemProps> = (props) => {
  const {
    // post: {currentShifts},
    // posts,
    currentShift: { securityGuard, guardedObject, startDate },
    workerId,
  } = props
  const [endShift, { isLoading }] = entityAPI.useEndShiftMutation()

  console.log(securityGuard, "security guard")


  const handleShiftEnd = async () => {
    await endShift({ guardId: securityGuard.id }).unwrap().then(() => {
      toast({
        title: 'Смена завершена',
      })
    }).catch(e => {
      toast({
        status: 'error',
        description: e.data || 'Ошибка',
      })
    })

  }


  return (
    <Box px={6} py={5} my={2} border="1px" borderColor="gray.100">
      {workerId
        ? guardedObject && (
          <Box>
            <HStack mb={3}>
              <Avatar size="sm" name={guardedObject.name} />
              <Text>{guardedObject.name}</Text>
            </HStack>
            <HStack spacing={6}>
              <ExternalLink href={`tel:${guardedObject.phone}`}>
                <Icons.IconPhone mr={2} />
                {guardedObject.phone}
              </ExternalLink>
            </HStack>
          </Box>
        )
        : securityGuard && (
          <Box>
            <HStack mb={3}>
              <Avatar size="sm" name={securityGuard.shortName} />
              <Text>{securityGuard.shortName}</Text>
            </HStack>
            <HStack spacing={6}>
              <ExternalLink href={`tel:${securityGuard.phone}`}>
                <Icons.IconPhone mr={2} />
                {securityGuard.phone}
              </ExternalLink>
              <Text>
                <Icons.IconPin mr={2} color="iconGray" />
                PIN: {securityGuard.pin}
              </Text>
            </HStack>
          </Box>
        )}
      <Divider />
      <SimpleGrid columns={2} gap={5} mb={5} align-items="flex-end"
        justifyContent="space-between">
        <Flex alignItems="center">
          <Pictogram />
          <Box>
            <Box mb={5}>
              <Text fontSize="xs" color="gray">
                Заступление на пост
              </Text>
              <Text>{moment(startDate * 1000).format('dd, D MMMM HH:mm')}</Text>
            </Box>
            <Box>
              <Text fontSize="xs" color="gray">
                Завершение смены
              </Text>
              <Text>В работе</Text>
            </Box>
          </Box>
        </Flex>
        {securityGuard?.salary && (
          <Box mt="59" className={isTimeAgoSeconds(securityGuard.salary.from)
            ? classes.PaymentSeconds
            : classes.Payment}>
            <Text fontSize="xs" color="gray">
              {moment(securityGuard.salary.from * 1000).toNow(true)}
            </Text>
            <Text>{getLocaleCurrency(securityGuard.salary.salary)}</Text>
          </Box>
        )}
      </SimpleGrid>
      <Button isLoading={isLoading} onClick={handleShiftEnd}>
        Завершить смену
      </Button>
    </Box>
  )
}

export const ShiftsList: React.FC<IShiftsListProps> = (props) => {
  const { currentShifts, entityId, posts, ...rest } = props




  if (!currentShifts?.length) {
    return null
  }

  return (
    <>
      {currentShifts.length && (
        <Text fontWeight="bold" fontSize="lg">
          Работник на посту
        </Text>
      )}
      <Box {...rest}>
        {currentShifts?.map((currentShift, idx) => (
          <ShiftItem post={posts[idx]} key={currentShift.id} entityId={entityId}
            currentShift={currentShift} />
        ))}
      </Box>
    </>
  )
}

export const WorkerShift: React.FC<IWorkerShiftProps> = (props) => {
  const { currentShift, workerId, ...rest } = props

  if (!currentShift) {
    return null
  }

  return (
    <>
      <Text>Посты работника</Text>
      <Box {...rest}>
        <ShiftItem workerId={workerId} currentShift={currentShift} />
      </Box>
    </>
  )
}
