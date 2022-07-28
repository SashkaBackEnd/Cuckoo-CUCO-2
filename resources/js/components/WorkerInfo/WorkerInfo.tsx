import React from 'react'

import {Link, useHistory} from 'react-router-dom'
import {Box, Divider, Heading, HStack} from '@chakra-ui/layout'
import {
  IconButton,
  Menu,
  MenuButton,
  MenuItem,
  MenuList,
  Link as ExternalLink,
  SimpleGrid,
  Text,
} from '@chakra-ui/react'
import {CloseIcon} from '@chakra-ui/icons'
import moment from 'moment'

import {Card, toast} from '@app/theme'
import {Icons} from '@components/UI/iconComponents'
import {DeleteButton} from '@components/UI/DeleteButton'
import {errorHandler} from '@app/errors'
import {IWorker} from '@models/worker'
import classes from './WorkerInfo.module.css'
import {PostLog} from '@components/PostLog'
import {WorkerShift} from '@components/ShiftsList'
import {workersAPI} from '@app/services/workerService'
import {usePermissions} from '@hooks/usePermissions'
import {ROUTE_NAMES} from '@app/Routes'

interface IWorkerInfoProps {
  worker: IWorker
}

export const WorkerInfo: React.FC<IWorkerInfoProps> = (props) => {
  const {
    worker: {
      id,
      pin,
      name,
      surname,
      patronymic,
      phone,
      birthDate,
      status,
      license,
      licenseRank,
      licenseToDate,
      drivingLicense,
      car,
      medicalBook,
      gun,
      workType,
      leftThings,
      depts,
      comment,
      log,
      currentShift,
      knewAboutUs,
    },
  } = props
  const history = useHistory()
  const [deleteHandler, {}] = workersAPI.useDeleteWorkerMutation()

  const {isEdit} = usePermissions(ROUTE_NAMES.workers)

  const handleDelete = async () => {
    await deleteHandler(id)
      .then(() => {
        toast({
          title: 'Работник удален',
        })
        history.push(`/workers`)
      })
      .catch((error) => {
        errorHandler(error)
      })
  }

  return (
    <Card>
      <HStack justifyContent="space-between">
        <Heading as="h4" size="md">
          {[surname, name, patronymic].filter((s) => s).join(' ')}
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
              icon={<Icons.IconDots />}
              variant="outline"
            />
            <MenuList zIndex={1000}>
              <MenuItem as={Link} to={`/workers/edit/${id}`} icon={<Icons.IconPencilFilled />}>
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
                title="Удаление Работника"
                description="Работник будет удален безвозвратно"
                deleteFn={handleDelete}
                icon={<CloseIcon />}
              >
                Удалить
              </MenuItem>
            </MenuList>
          </Menu>
        )}
      </HStack>
      <Box className={classes.Info}>
        <ExternalLink href={`tel:${phone}`} className={classes.InfoItem}>
          <Icons.IconPhone mr={2} />
          {phone}
        </ExternalLink>
        <Text className={classes.InfoItem}>
          <Icons.IconPin color="iconGray" mr={2} />
          PIN: {pin}
        </Text>
      </Box>
      <Divider />
      <SimpleGrid columns={1} spacingY={3}>
        <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
          <Text color="gray">Дата рождения</Text>
          <Text>{moment.unix(birthDate).format('DD.MM.YYYY')}</Text>
        </SimpleGrid>
        <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
          <Text color="gray">Статус</Text>
          <Text>{status}</Text>
        </SimpleGrid>
        <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
          <Text color="gray">УЛЧО</Text>
          <Text>{license ? 'Есть' : 'Нет'}</Text>
        </SimpleGrid>
        {!!licenseRank && (
          <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
            <Text color="gray">Разряд УЛЧО</Text>
            <Text>{licenseRank}</Text>
          </SimpleGrid>
        )}
        {!!license && (
          <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
            <Text color="gray">Срок действия</Text>
            <Text>до {moment.unix(licenseToDate).format('DD.MM.YYYY')}</Text>
          </SimpleGrid>
        )}
        <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
          <Text color="gray">Водительские права</Text>
          <Text>{drivingLicense ? 'Есть' : 'Нет'}</Text>
        </SimpleGrid>
        {!!car && (
          <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
            <Text color="gray">Личное авто</Text>
            <Text>{car}</Text>
          </SimpleGrid>
        )}
        {!!medicalBook && (
          <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
            <Text color="gray">Медицинская книжка</Text>
            <Text>{medicalBook}</Text>
          </SimpleGrid>
        )}
        {!!gun && (
          <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
            <Text color="gray">Личное оружие</Text>
            <Text>{gun}</Text>
          </SimpleGrid>
        )}
        <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
          <Text color="gray">Тип работы</Text>
          <Text>{workType}</Text>
        </SimpleGrid>
        {!!leftThings && (
          <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
            <Text color="gray">Оставленные вещи</Text>
            <Text>{leftThings}</Text>
          </SimpleGrid>
        )}
        {!!depts && (
          <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
            <Text color="gray">Судимости, долги, алименты</Text>
            <Text>{depts}</Text>
          </SimpleGrid>
        )}
        {!!comment && (
          <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
            <Text color="gray">Комментарий</Text>
            <Text>{comment}</Text>
          </SimpleGrid>
        )}
        {!!knewAboutUs && (
          <SimpleGrid templateColumns="1fr 2fr" spacingX={7}>
            <Text color="gray">Откуда узнал о нас</Text>
            <Text>{knewAboutUs}</Text>
          </SimpleGrid>
        )}
      </SimpleGrid>
      <Divider />
      <WorkerShift workerId={id} currentShift={currentShift} />
      <PostLog log={log} />
    </Card>
  )
}
