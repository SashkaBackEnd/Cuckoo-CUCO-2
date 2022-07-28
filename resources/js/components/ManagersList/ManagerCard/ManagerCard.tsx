import React, { useMemo } from 'react'
import { Link } from 'react-router-dom'
import {
  Avatar,
  Box,
  Divider,
  Flex,
  HStack,
  Link as ExternalLink,
  LinkBox,
  LinkOverlay,
  Text,
} from '@chakra-ui/react'
import { IManager } from '@models/manager'
import { ItemList } from '@app/theme'
import { Icons } from '../../UI/iconComponents'
import classes from './ManagerCard.module.css'
import { entityCountWordInRussian } from '@app/helpers/wordList'
import { getFullName, unmaskPhone } from '@app/helpers'
import { normalizeStatusData } from '@app/helpers/normalizeStatusData'
import { maskPhone } from '@app/helpers/maskPhone'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'


interface IWorkerCardProps extends IManager {
  isActive?: boolean

}


export const ManagerCard: React.FC<IWorkerCardProps> = (props) => {
  const {id, entities,   patronymic, phone, name, surname, isActive, pin, access, status } = props

  const normalizedStatus = useMemo(() => normalizeStatusData(status, true), [status])

  return (
    <LinkBox as={ItemList}>
      <Box>
        <LinkOverlay as={Link} to={`/managers/${id}`}>
          <Flex alignItems={"center"} justifyContent='space-between' >
             <HStack spacing={3} className={classes.HStack}>
               <Avatar size="sm" name={`${surname} ${name}`} />
               <Text fontWeight="bold">{getFullName(surname, name, patronymic)}</Text>
             </HStack>
             {normalizedStatus}
          </Flex>
        </LinkOverlay>
        <Divider />
        <HStack spacing={8} alignItems={"baseline"}>
          <ExternalLink href={`tel:${phone}`} className={classes.Info}>
            <Icons.IconPhone w={"20px"} h={"20px"} mr={2} />
            {maskPhone(phone)}
          </ExternalLink>
          <HStack  >
            <Icons.IconKey color="iconGray" mr={2} />
             <Text>  {entities.length} {entityCountWordInRussian(entities.length)} </Text>
          </HStack>
        </HStack>
      </Box>
    </LinkBox>
  )
}
