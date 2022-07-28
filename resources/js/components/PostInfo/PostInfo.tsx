import React, { useCallback, useMemo } from 'react'

import { Divider, Heading, HStack, Stack } from '@chakra-ui/layout'
import {
  Badge, Box,
  Button,
  IconButton,
  Link as ExternalLink,
  Menu,
  MenuButton,
  MenuItem,
  MenuList,
  Text,
} from '@chakra-ui/react'
import { Link, useHistory, useParams } from 'react-router-dom'
import { CloseIcon } from '@chakra-ui/icons'

import { Card, toast } from '@app/theme'
import { IPost } from '@models/post'
import { Icons } from '@components/UI/iconComponents'
import { DeleteButton } from '@components/UI/DeleteButton'
import { LastCheck } from '@components/LastCheck'
import { ShiftsList } from '@components/ShiftsList'
import { PostLog } from '@components/PostLog'
import { getWorkTime } from '@app/helpers'
import { entityAPI } from '@app/services'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'
import classes from './postinfo.module.css'


interface IPostInfo {
  post: IPost
  posts: IPost[]
}


export const PostInfo: React.FC<IPostInfo> = (props) => {
  const {
    posts,
    post: {
      id,
      log,
      isCentral,
      lastListCheck,
      standardWork,
      currentShifts,
      name,
      phone,
      entityId,
    },
  } = props

  const { isEdit } = usePermissions(ROUTE_NAMES.objects)

  const history = useHistory()
  const [deletePost] = entityAPI.useDeletePostMutation()
  const [checkPost, { isLoading }] = entityAPI.useCheckPostMutation()


  const { entityId: objectId }: never = useParams()
  const { data: entity } = entityAPI.useFetchEntityByIdQuery(
    entityId)

  console.log(entity, "entity")
  console.log(currentShifts, 'currentShifts')



  const handlePostDelete = useCallback(async () => {
    await deletePost({ id, entityId }).unwrap().then(() => {
      toast({
        title: 'Пост удален',
      })
      history.push(`/entities/${entityId}`)
    })
  }, [deletePost, history, id, entityId])

  const handlePostCheck = useCallback(async () => {
    await checkPost({ id, entityId }).unwrap().then(() => {
      toast({
        title: 'Проверка запущена',
      })
    })
  }, [checkPost, id, entityId])


  const workTime = useMemo(() => {
    return getWorkTime(standardWork)
  }, [standardWork])


  return (
    <Card>
      {isCentral && <Badge colorScheme="blue">Центральный пост</Badge>}
      <HStack mb={5} justifyContent="space-between">
        <Heading as="h4" size="lg">
          {name}
        </Heading>
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
              title="Удаление поста"
              description="Пост будет удален безвозвратно"
              deleteFn={handlePostDelete}
              icon={<CloseIcon />}
            >
              Удалить
            </MenuItem>
          </MenuList>
        </Menu>}
      </HStack>
      <Stack mb={5} direction={{ base: "column", md: 'column', xl: 'column', "2xl": 'row' }} spacing='25.5px'>
        <HStack >
          {workTime.day && <Icons.IconTime color="iconGray" mr={2} />}
          {workTime?.time.length
            ? <Text>{`${workTime.day}, ${workTime.time.join(' ')}`}</Text>
            : '-'}
        </HStack>

        <Box >
          <ExternalLink w="full" href={`tel:${phone}`}>
            <Icons.IconPhone mr={2} /> {phone}
          </ExternalLink>
        </Box>

        <HStack >
          <Icons.IconRuble color="iconGray" />
          <Text ml={'11.5px'} fontSize="12px">  {workTime?.salary} </Text>
        </HStack>

      </Stack>


      <Button isLoading={isLoading} onClick={handlePostCheck}
        colorScheme="green" mb={5}>
        Проверить пост
      </Button>
      <LastCheck lastListCheck={lastListCheck} />
      <Divider />
      <ShiftsList posts={posts} entityId={entityId} currentShifts={currentShifts} />
      <PostLog log={log} />
    </Card>
  )
}
