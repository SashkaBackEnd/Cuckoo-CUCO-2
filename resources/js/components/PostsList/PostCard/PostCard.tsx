import React, { useCallback, useEffect, useMemo, useState } from 'react'

import {
  Circle,
  Divider,
  HStack,
  LinkBox,
  LinkOverlay,
} from '@chakra-ui/layout'
import {
  Avatar,
  Badge,
  Box,
  IconButton,
  Link as ExternalLink,
  Menu,
  MenuButton,
  MenuItem,
  MenuList,
  SimpleGrid,
  Text,
} from '@chakra-ui/react'
import { Link, useHistory } from 'react-router-dom'
import { CloseIcon } from '@chakra-ui/icons'
import moment from 'moment'

import { IPost } from '@models/post'
import { toast } from '@app/theme'
import { getLocaleCurrency, getWorkTime } from '@app/helpers'
import { Icons } from '@components/UI/iconComponents'
import { DeleteButton } from '@components/UI/DeleteButton'
import { LastCheck } from '@components/LastCheck'
import Pictogram from '@components/UI/Pictogram/Pictogram'
import classes from './PostCard.module.css'
import { entityAPI } from '@app/services'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'


interface IPostCardProps {
  isActive?: boolean
  isCentral?: boolean
  post: IPost
}


export const PostCard: React.FC<IPostCardProps> = (props) => {

  const [state, setState] = useState(false)

  const { isEdit } = usePermissions(ROUTE_NAMES.objects)
  const {
    post: {
      id,
      name,
      lastListCheck,
      standardWork,
      currentShifts,
      phone,
      entityId,
    },
    isActive,
    isCentral,
  } = props
  const [deletePost, { error }] = entityAPI.useDeletePostMutation()
  const history = useHistory()

  const handlePostDelete = useCallback(async () => {
    await deletePost({ id, entityId }).unwrap().then(() => {
      toast({
        title: 'Пост удален',
      })
      history.push(`/entities/${entityId}`)
    }).catch(() => {
      toast({
        status: 'error',
        title: 'Ошибка удаления поста',
      })
    })
  }, [deletePost, id, entityId])

  const workTime = useMemo(() => {
    return getWorkTime(standardWork)
  }, [standardWork])

  useEffect(() => {
    const id = setTimeout(() => {
      setState(p => !p)
    }, 3000)

    return () => {
      clearTimeout(id)
    }

  }, [state])
  console.log(lastListCheck)
  return (
    <LinkBox as="div"
      className={[classes.card, isActive && classes.active].join(' ')}>
      <div className={classes.post}>
        <HStack mb={4} justifyContent="space-between">
          <HStack spacing={4}>
            <Badge colorScheme="gray" color="gray" mb={0}>
              <b>№ {id}</b>
            </Badge>
            <LastCheck lastListCheck={lastListCheck} />
          </HStack>
          {isEdit && <Menu>
            <MenuButton
              isRound
              zIndex={1}
              size="sm"
              as={IconButton}
              aria-label="Опции"
              colorScheme="gray"
              icon={<Icons.IconDots />}
              variant="outline"
            />
            <MenuList zIndex={1000}>
              <MenuItem as={Link} to={`/entities/${entityId}/edit/${id}`}
                icon={<Icons.IconPencilFilled />}>
                Редактировать
              </MenuItem>
              <MenuItem
                as={DeleteButton}
                border="none"
                rounded={0}
                colorScheme="gray"
                variant="outline"
                size="md"
                fontWeight="normal"
                title="Удаление объекта"
                description="Пост будет удален безвозвратно"
                deleteFn={handlePostDelete}
                icon={<CloseIcon />}
              >
                Удалить
              </MenuItem>
            </MenuList>
          </Menu>}
        </HStack>
        {isCentral && <Badge colorScheme="blue">Центральный пост</Badge>}
        <LinkOverlay as={Link} to={`/entities/${entityId}/${id}`}>
          <HStack alignItems="center" spacing={3}>
            <Circle size="36px" bg="red.300" color="white">
              <Icons.IconShield w={4} h={6} />
            </Circle>
            <Text fontSize="md" fontWeight="bold">
              {name}
            </Text>
          </HStack>
        </LinkOverlay>
        <Divider />
        <SimpleGrid columns={1} spacing={3}>
          <ExternalLink href={`tel:${phone}`}>
            <Icons.IconPhone mr={2} /> {phone}
          </ExternalLink>
          <Text>
            <Icons.IconTime color="iconGray" mr={2} />
            {workTime?.time.length ? `${workTime.day}, ${workTime.time.join(
              ' ')}` : '-'}
          </Text>
          <Text>
            <Icons.IconRuble color="iconGray" mr={2} />
            {workTime?.salary}
          </Text>
        </SimpleGrid>
      </div>
      <div className={classes.guard}>
        {currentShifts?.length ? (
          currentShifts?.map((shift) => (
            <Box key={shift.id}>
              {shift.securityGuard && (
                <HStack mb={3}>
                  <Avatar size="sm" name={shift.securityGuard.shortName} />
                  <Text>{shift.securityGuard.shortName}</Text>
                </HStack>
              )}
              <Box className={classes.ShiftInfo}>
                <Pictogram />
                <Box>
                  <Box mb={5}>
                    <Text fontSize="xs" color="gray">
                      Заступление на пост
                    </Text>
                    <Text>{moment(shift.startDate * 1000).
                      format('dd, D MMMM HH:mm')}</Text>
                  </Box>
                  <Box>
                    <Text fontSize="xs" color="gray">
                      Завершение смены
                    </Text>
                    <Text>В работе</Text>
                  </Box>
                </Box>
              </Box>
              {shift.securityGuard.salary && (
                <Box textAlign="right" mt="10">
                  <Text fontSize="xs" color="gray">
                    {moment(shift.securityGuard.salary.from * 1000).toNow(true)}
                  </Text>
                  <Text>{getLocaleCurrency(
                    shift.securityGuard.salary.salary)} </Text>
                </Box>
              )}
            </Box>
          ))
        ) : (
          <HStack spacing={2}>
            <Circle size="32px" bg="gray.500" color="gray.400">
              <Icons.IconMenuWorkers />
            </Circle>
            <Text fontWeight="bold" color="boxDividerColor">
              Нет охраны
            </Text>
          </HStack>
        )}
      </div>
    </LinkBox>
  )
}
