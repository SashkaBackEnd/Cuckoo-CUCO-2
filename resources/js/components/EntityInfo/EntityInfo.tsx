import React, { useCallback } from 'react'
import {
  Badge,
  Button,
  Divider,
  Flex,
  Heading,
  IconButton,
  Menu,
  Text,
  MenuButton,
  MenuItem,
  MenuList,
  SimpleGrid,
  VisuallyHidden,
  Link as ExternalLink,
  Collapse,
  useDisclosure,
  LinkOverlay,
  HStack,
  Avatar,
  VStack,
} from '@chakra-ui/react'
import { saveAs } from 'save-as'
import axios from 'axios'
import { Link, useHistory } from 'react-router-dom'
import { ChevronDownIcon, ChevronUpIcon, CloseIcon } from '@chakra-ui/icons'

import classes from './EntityInfo.module.css'
import { IEntity } from '@models/entity'
import { Icons } from '@components/UI/iconComponents'
import { DeleteButton } from '@components/UI/DeleteButton'
import { errorHandler } from '@app/errors'
import { Card, toast } from '@app/theme'
import { entityAPI, managerAPI } from '@app/services'

import AttachManagerModal from '@components/EntityInfo/AttachManagerModal'

import AttachedManagers from '@components/EntityInfo/AttachedManagers'
import { useCurrentUser } from '@hooks/useCurrentManager'

import { maskPhone } from '@app/helpers/maskPhone'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'
import { IManager } from '@models/manager'


interface IEntityInfoProps {
  entity: IEntity
}


export const EntityInfo: React.FC<IEntityInfoProps> = (props) => {
  const {
    entity: {
      id,
      name,
      customerName,
      dialingStatus,
      comment,
      customers,
      address,
      phone,
      centralPost,
    },
  } = props

  const { isEdit } = usePermissions(ROUTE_NAMES.objects)
  const { isEdit: hasAccessToManagers } = usePermissions(ROUTE_NAMES.managers)



  const { isOpen, onToggle } = useDisclosure()
  const [toggleDial, { isLoading }] = entityAPI.useToggleDialMutation()
  const [deleteEntity] = entityAPI.useDeleteEntityMutation()

  const { manager: currentUser } = useCurrentUser()

  let managers: IManager[]

  if (hasAccessToManagers) {
    const { data: data, error } = managerAPI.useFetchAllManagersQuery(1)
    managers = data
  }

  const attachedManagers = managers?.filter((man) => {
    return man.entities.some((entity) => entity?.id === id)
  })


  const history = useHistory()

  const exportEntityHandler = () => {
    axios.get(`/api/entities/${id}/export`, { responseType: 'blob' }).
      then(({ data }) => {
        const blob = new Blob(
          [data], { type: 'application/vnd.ms-excel;charset=utf-8' })
        saveAs(blob, `entity_${id}.xls`)
      }).
      catch((error) => {
        errorHandler(error)
      })
  }

  const importEntityHandler = (files) => {
    const formData = new FormData()
    formData.append('file', files[0])

    axios.post(`/api/entities/${id}/import`, formData).then(() => {
      toast({ title: 'Документ импортирован' })
    }).catch((error) => {
      errorHandler(error)
    })
  }

  const toggleDialHandler = useCallback(
    async (set) => {
      await toggleDial({ id, set }).unwrap().then(() => {
        toast({ title: set ? 'Обзвон возобновлен' : 'Обзвон приостановлен' })
      })
    },
    [toggleDial, id],
  )

  const deleteEntityHandler = useCallback(async () => {
    await deleteEntity(id).unwrap().then(() => {
      toast({ title: 'Объект удален' })
      history.push(`/entities`)
    })
  }, [deleteEntity, id, history])

  return (
    <Card mb={6}>
      <SimpleGrid
        columns={[1, 2]}
      >
        <div className={classes.info}>
          {centralPost &&
            <Badge colorScheme="blue">С центральным постом</Badge>}
          <Flex justify="space-between">
            <Heading as="h4" size="lg" mb="2">
              {name}
            </Heading>
            {isEdit && (
              <Menu>
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
                  <MenuItem as={Link} to={`/entities/edit/${id}`}
                            icon={<Icons.IconPencilFilled/>}>
                    Редактировать
                  </MenuItem>
                  <MenuItem as="label" icon={<Icons.IconImport/>}>
                    Импорт объекта
                    <VisuallyHidden>
                      <input
                        type="file"
                        multiple={false}
                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                        onChange={(e) => importEntityHandler(e.target.files)}
                      />
                    </VisuallyHidden>
                  </MenuItem>
                  <MenuItem onClick={exportEntityHandler} type="button"
                            icon={<Icons.IconExport/>}>
                    Экспорт объекта
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
                    description="Объект будет удален безвозвратно"
                    deleteFn={deleteEntityHandler}
                    icon={<CloseIcon/>}
                  >
                    Удалить
                  </MenuItem>
                </MenuList>
              </Menu>
            )}
          </Flex>
          <p className={classes.info__description}>{customerName}</p>

          <div className={classes.info__contacts}>
            <p>
              <Icons.IconAddress color="gray"/> {address}
            </p>
            <a href={`tel:${phone}`}>
              <Icons.IconPhone/> {maskPhone(phone)}
            </a>
          </div>
          <Divider/>
          <div className={classes.MoreInfo}>
            <Text fontWeight="bold"
                  _hover={{ cursor: 'pointer', color: 'darkgray' }}
                  onClick={onToggle} mb={3}>
              Подробнее об объекте
              {isOpen ? <ChevronUpIcon/> : <ChevronDownIcon/>}
            </Text>
          </div>
          <Collapse in={isOpen} animateOpacity>
            <SimpleGrid columns={1} spacingY={3} mb={3}>
              <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
                <Text>ID объекта</Text>
                <Text>{id}</Text>
              </SimpleGrid>
              <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
                <Text>Служебный телефон</Text>
                <ExternalLink href={`tel:${phone}`}>{maskPhone(
                  phone)}</ExternalLink>
              </SimpleGrid>
              {customers.map((customer, index) => {
                return (
                  <SimpleGrid key={customer.id} templateColumns="1fr 2fr"
                              spacingX={7}>
                    <Text>Контакт заказчика {index + 1}</Text>
                    <Text>{`${customer.contact} (${customer.name})`}</Text>
                  </SimpleGrid>
                )
              })}
              {!!comment && (
                <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
                  <Text>Комментарий</Text>
                  <Text>{comment}</Text>
                </SimpleGrid>
              )}
            </SimpleGrid>
          </Collapse>
          {dialingStatus ? (
            <Button
              isLoading={isLoading}
              onClick={() => toggleDialHandler(false)}
              type="button"
              colorScheme="red"
              variant="solid"
            >
              Приостановить обзвон
            </Button>
          ) : (
            <Button
              isLoading={isLoading}
              onClick={() => toggleDialHandler(true)}
              type="button"
              colorScheme="green"
              variant="solid"
            >
              Возобновить обзвон
            </Button>
          )}
        </div>
        {hasAccessToManagers &&
          <VStack align="start" ml={{ base: '0', md: '3' }}>
            <Text color="black" fontSize="14px" fontWeight="700"
                  mt={{ base: '3', md: '0' }}>
              Менеджеры ({attachedManagers?.length || 0})
            </Text>
            <VStack maxH="180px" w={'full'} overflowY="scroll">
              {attachedManagers && (
                attachedManagers?.map((attManager) => {
                  return <AttachedManagers key={attManager.id}
                                           manager={attManager}/>
                })
              )}

            </VStack>
            {isEdit && <AttachManagerModal/>}
          </VStack>}

      </SimpleGrid>
    </Card>
  )
}
