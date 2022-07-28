import React, { useEffect, useMemo, useState } from 'react'

import { Link, useHistory, useParams } from 'react-router-dom'
import { Box, Divider, Heading, HStack, Stack } from '@chakra-ui/layout'
import {
  Button,
  IconButton,
  Input,
  Link as ExternalLink,
  Menu,
  MenuButton,
  MenuItem,
  MenuList,
  Modal,
  ModalBody,
  ModalCloseButton,
  ModalContent,
  ModalFooter,
  ModalHeader,
  ModalOverlay,
  Text,
  useDisclosure,
} from '@chakra-ui/react'
import { CloseIcon } from '@chakra-ui/icons'

import { Card, toast } from '@app/theme'
import { Icons } from '@components/UI/iconComponents'
import { DeleteButton } from '@components/UI/DeleteButton'
import { errorHandler } from '@app/errors'
import { IManager } from '@models/manager'
import classes from '../WorkerInfo/WorkerInfo.module.css'
import {  managerAPI  } from '@app/services'
import { ManagerShift } from '@components/ManagerShift'
import { PostLog } from '@components/PostLog'
import { normalizeStatusData } from '@app/helpers/normalizeStatusData'
import { maskPhone } from '@app/helpers/maskPhone'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'


interface IManagerInfoProps {
  manager: IManager
}


export const ManagerInfo: React.FC<IManagerInfoProps> = (props) => {
  const {
    manager: { id, name, surname, patronymic, phone, entities, pin, log, guarded_objects_count, worker_count, status  },
  } = props

  const normalizedStatus = useMemo(() => normalizeStatusData(status, false),[status])

  const [isDeactivated,  setIsDeactivated] = useState<boolean>(false)

  const history = useHistory()
  const [
    deleteHandler, {
      error,

    },
  ] = managerAPI.useDeleteManagerMutation()
  const [deactivateManager, {isLoading: isLoadingDeactivation}] = managerAPI.useDeactivateManagerMutation()
  const [activateManager, {isLoading: isLoadingActivation}] = managerAPI.useActivateManagerMutation()
  const { isOpen, onOpen, onClose } = useDisclosure()
  const [value, setValue] = useState()

  let obj = 'Закрепленных объектов'
  let post = 'Число постов'

  const { isEdit } = usePermissions(ROUTE_NAMES.managers)



  const handleDelete = async () => {
    await deleteHandler(id).unwrap().then(() => {
      toast({
        title: 'Работник удален',
      })
      history.push(`/managers`)

    }).catch((error) => {
      errorHandler(error)
    })
  }
  const handleClick = async () => {
    if(isDeactivated) {
        await activateManager({user_id: id, message: value})
        .unwrap().then(() => {
          toast({
            title: 'Manager Activated',
          })
          onClose()
        }).catch((error) => {
          errorHandler(error)
          toast({
            status: "error",
            title: 'Ошибка активации',
          })
        })
    } else {
      await deactivateManager(
        { user_id: id, message: value })
      .unwrap().then(() => {
        toast({
          title: 'Manager deactivated',
        })
        onClose()
      }).catch((error) => {
        errorHandler(error)
        toast({
          status: "error",
          title: 'Ошибка деактивации',
        })
      })
    }

  }


  useEffect(() => {
    let activated = status === 0
    setIsDeactivated(activated)
  }, [status])


  return (
    <Card>

        {normalizedStatus}

      <HStack justifyContent="space-between" mt="1.3rem">
        <Heading as="h4" size="md">
          {[name, surname, patronymic].filter((s) => s).join(' ')}
        </Heading>
        { isEdit && <Menu>
          <MenuButton
            isRound
            zIndex={1}
            size="sm"
            as={IconButton}
            aria-label="Опции"
            colorScheme="gray"
            icon={<Icons.IconDots/>}
            variant="outline"
          />
          <MenuList zIndex={1000}>
            <MenuItem as={Link} to={`/managers/edit/${id}`}
                      icon={<Icons.IconPencilFilled/>}>
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
              title="Удаление Менеджера "
              description="Менеджер будет удален безвозвратно"
              deleteFn={handleDelete}
              icon={<CloseIcon/>}
            >
              Удалить
            </MenuItem>
          </MenuList>
        </Menu>}
      </HStack>
      <Box className={classes.Info} mt="1.3rem">
        <ExternalLink href={`tel:${phone}`} className={classes.InfoItem}>
          <Icons.IconPhone mr={2}/>
          {maskPhone(phone)}
        </ExternalLink>
      </Box>
      <Divider/>
      <Stack spacing="1rem">
        <Text fontWeight="400" fontSize="14px">
          {obj} {entities?.length}
        </Text>
        <Text fontWeight="400" fontSize="14px">
          {post} {guarded_objects_count || 0}
        </Text>
        <Button colorScheme={isDeactivated ? "green" : "red"} width="246px" variant="solid"
                onClick={onOpen}>
          { isDeactivated ? "Активировать" : "Деактивировать" } менеджера
        </Button>
        <Modal isOpen={isOpen} onClose={onClose}>
          <ModalOverlay/>
          <ModalContent>
            <ModalHeader> {isDeactivated ? "Активация": "Деактивация" } менеджера</ModalHeader>
            <ModalCloseButton/>
            <ModalBody>
              <Text fontSize="13px">Например, "прогулы смен"</Text>
              <Input type="text"
                     onChange={(e: any) => setValue(e.target.value)}/>
            </ModalBody>

            <ModalFooter>
              <Button colorScheme={isDeactivated ? "green" : "red"} w="full" isLoading={isLoadingActivation || isLoadingDeactivation} onClick={handleClick}>
                { isDeactivated ? "Активировать" : "Деактивировать" } менеджера
              </Button>
            </ModalFooter>
          </ModalContent>
        </Modal>
        <Divider/>
      </Stack>


      { !!entities?.length &&  <>
        <Divider/>  <ManagerShift guardedObjectCount={guarded_objects_count}
                     workerCount={worker_count} managerId={id}
                     entities={entities}/>
      </> }

      <PostLog log={log}/>


    </Card>
  )
}
