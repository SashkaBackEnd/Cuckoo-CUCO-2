import React from 'react'
import { Link } from 'react-router-dom'
import { Avatar, Box, HStack, Link as ExternalLink } from '@chakra-ui/react'
import { Divider, LinkBox, LinkOverlay, Text } from '@chakra-ui/layout'
import { IWorker } from '../../../models/worker'
import { ItemList } from '../../../theme'
import { getFullName } from '../../../helpers'
import { Icons } from '../../UI/iconComponents'
import classes from './WorkerCard.module.css'
import { LastCheck } from '../../LastCheck'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'


interface IWorkerCardProps extends IWorker {
  isActive?: boolean
}

export const WorkerCard: React.FC<IWorkerCardProps> = (props) => {
  const {id, pin, name, surname, patronymic, phone, lastListCheck, currentShift,} = props
  return (
    <LinkBox as={ItemList}>
      <Box>
        <LinkOverlay as={Link} to={`/workers/${id}`}>
          <HStack spacing={3} className={classes.HStack}>
            <Avatar size="sm" name={`${surname} ${name}`} />
            <Text fontWeight="bold">{getFullName(surname, name, patronymic)}</Text>
            <LastCheck lastListCheck={lastListCheck} className={classes.LastCheck} />
          </HStack>
        </LinkOverlay>
        <Divider />
        <HStack spacing={8}>
          <ExternalLink href={`tel:${phone}`} className={classes.Info}>
            <Icons.IconPhone mr={2} />
            {phone}
          </ExternalLink>
          <Text className={classes.Info}>
            <Icons.IconPin color="iconGray" mr={2} />
            PIN: {pin}
          </Text>
          {!!currentShift &&  <Text className={classes.Info}>
            <Icons.IconEntity color="iconGray" mr={2} />
          </Text> }

        </HStack>
      </Box>
    </LinkBox>
  )
}
