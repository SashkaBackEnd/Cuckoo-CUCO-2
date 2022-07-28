import React, {useCallback} from 'react'
import {
  Avatar,
  Link as ExternalLink,
  Box,
  Divider,
  Flex,
  HStack,
  LinkOverlay,
  Image,
  Text,
  VStack,
  Button,
} from '@chakra-ui/react'
import {Icons} from '@components/UI/iconComponents'
import classes from './LastInfo.module.css'
import {IWorker} from '@models/worker'
import Pictogram from '@components/UI/Pictogram/Pictogram'
import moment from 'moment'
import {entityAPI} from '@app/services'
import {toast} from '@app/theme'
import { ICurrentShift, IPost } from '@models/post'

interface IWorkerShiftProps {
  workers: IWorker[]
  workerId: string
  entityId?: string
}

export const LastInfo: React.FC<IWorkerShiftProps> = (props) => {
  const {
    workers,
     workerId,
     entityId,

    } = props
  const [endShift, {isLoading}] = entityAPI.useEndShiftMutation()


  const handleShiftEnd = useCallback(async () => {
    endShift({ guardId: workerId })
      .unwrap()
      .then(() => {
        toast({
          title: 'Смена завершена',
        })
      })
  }, [endShift, workerId, entityId])

  return (
    <Box px={6} py={5} my={2} border="1px" borderColor="gray.100">
      {workers?.map((work) => (
        <Box key={work.id}>
          <Flex justifyContent="space-between" mt="1.4rem">
            <HStack spacing={3} className={classes.HStack}>
              <Avatar size="sm" name={`${work.surname} ${work.name}`} />
              {/* <Text fontWeight="bold">{name}</Text> */}
              <Text fontWeight="bold">{work.name}</Text>
            </HStack>
          </Flex>
          <HStack spacing={8} mt="1.4rem">
            <ExternalLink href={`tel:${work.phone}`} className={classes.Info}>
              <Icons.IconPhone mr={2} />
              {work.phone}
            </ExternalLink>
            <Text className={classes.Info}>
              <Icons.IconKey color="iconGray" mr={2} />
              PIN: {work.pin}
            </Text>
          </HStack>
        </Box>
      ))}

      <Divider />
      <Flex justifyContent="space-between">
      <HStack>
        {/* <Image src="/images/svg/frame.svg" /> */}
        <Pictogram />
        <VStack>
          <Text mt={8}>Заступление на пост</Text>
          {/* <Text fontSize="12px">
            {workTime?.time.length ? `${workTime.day}, ${workTime.time.join(' ')}` : '-'}
          </Text> */}
          <Text>Завершение смены</Text>
          <Text color="blue" fontSize="14px" fontWeight="400">
            В работе
          </Text>
        </VStack>
      </HStack>
      <Box mt='4.5rem'>
          <Text fontSize="12px">{/* {moment(startDate * 1000).format('dd, D MMMM HH:mm')} */}2 ч 30 мин</Text>
          <Text fontSize="16px">{/* {moment(startDate * 1000).format('dd, D MMMM HH:mm')} */}2 300 ₽</Text>
        </Box>
      </Flex>
        <Button
           mt={2}
           backgroundColor="#DCE5FB"
           color="#3E74F4"
           w="200px"
           variant="solid"
           isLoading={isLoading}
           onClick={handleShiftEnd}
           >
          Завершить смену
        </Button>
    </Box>
  )
}
