import React, { useCallback, useEffect } from 'react'

import axios from 'axios'
import { saveAs } from 'save-as'
import { Link } from 'react-router-dom'
import { Divider, LinkBox, LinkOverlay } from '@chakra-ui/layout'
import {
  Badge, color, HStack,
  IconButton,
  Menu,
  MenuButton,
  MenuItem,
  MenuList, Text,
  VisuallyHidden,
} from '@chakra-ui/react'

import { DeleteButton } from '@components/UI/DeleteButton'
import { Icons } from '@components/UI/iconComponents'
import { CloseIcon } from '@chakra-ui/icons'
import classes from './EntityCard.module.css'
import { toast } from '@app/theme'
import { errorHandler } from '@app/errors'
import { IEntity } from '@models/entity'
import { entityAPI } from '@app/services'
import {
  guardedObjectsWordInRussian,
  managersWordInRussian,
  workersWordInRussian,
} from '@app/helpers/wordList'
import moment from 'moment'
import { getCheckDateTime } from '@app/helpers/getCheckDateTime'
import { useCurrentUser } from '@hooks/useCurrentManager'
import {
  IAccessObj,
  PermissionTypes,
} from '@components/AccessSettings/ManagerInAccess'
import { maskPhone } from '@app/helpers/maskPhone'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'


interface IEntityCardProps {
  entity: IEntity
}


export const EntityCard: React.FC<IEntityCardProps> = (props) => {

  const {
    entity: {
      id,
      name,
      customerName,
      address,
      phone,
      posts,
      centralPost,
      dialingStatus,
      managers_count,
      workers_count,
      last_check,
    },
  } = props


  const [toggleDial, { isLoading }] = entityAPI.useToggleDialMutation()
  const [deleteEntity] = entityAPI.useDeleteEntityMutation()

  const { isEdit } = usePermissions(ROUTE_NAMES.objects)  

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
      toast({
        title: 'Документ импортирован',
      })
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
    })
  }, [deleteEntity, id])

  // console.log(last_check.type)
  return (
    <LinkBox as="article" className={classes.card}>
      <div className={classes.card__header}>

        <Text
          className={classes.card__status}
          color={last_check && last_check.type === 'bad' ? 'red' : '#4CC557'} >
          <Icons.IconCircle mr={1} w={3} h={3} />
          {last_check && getCheckDateTime(last_check)}
        </Text>
        <div>
          {dialingStatus ? (
            <IconButton
              isRound
              zIndex={1}
              size="sm"
              isLoading={isLoading}
              aria-label="Остановить"
              colorScheme="red"
              onClick={() => toggleDialHandler(false)}
              icon={<Icons.IconStop />}
            />
          ) : (
            <IconButton
              isRound
              zIndex={1}
              size="sm"
              isLoading={isLoading}
              aria-label="Возобновить"
              colorScheme="green"
              onClick={() => toggleDialHandler(true)}
              icon={<Icons.IconStart />}
            />
          )}
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
              <MenuItem as={Link} to={`/entities/edit/${id}`}
                icon={<Icons.IconPencilFilled />}>
                Редактировать
              </MenuItem>
              <MenuItem as="label" icon={<Icons.IconImport />}>
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
                icon={<Icons.IconExport />}>
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
                icon={<CloseIcon />}
              >
                Удалить
              </MenuItem>
            </MenuList>
          </Menu>}
        </div>
      </div>
      <span className={classes.card__icon}>
        <Icons.IconEntity color="#fff" />
      </span>
      {centralPost && <Badge colorScheme="blue">С центральным постом</Badge>}
      <LinkOverlay as={Link} to={`/entities/${id}`}>
        <p className={classes.card__title}>{name}</p>
      </LinkOverlay>
      <p className={classes.card__subtitle}>{customerName}</p>
      <Divider />
      <div className={classes.card__contacts}>
        <p>
          <Icons.IconAddress color="gray" /> {address}
        </p>
        <a href={`tel:${phone}`}>
          <Icons.IconPhone /> {maskPhone(phone)}
        </a>
        <p>
          <Icons.IconShield color="gray" /> {posts.length} {guardedObjectsWordInRussian(+posts.length)}
        </p>
      </div>
      <div className={classes.card__peoples}>
        <p>
          <Icons.IconMenuManagers color="gray" />
          {managers_count} {managersWordInRussian(managers_count)}
        </p>
        <Divider orientation="vertical" />
        <p>
          <Icons.IconMenuWorkers color="gray" />
          {workers_count} {workersWordInRussian(workers_count)}
        </p>
      </div>
    </LinkBox>
  )
}
