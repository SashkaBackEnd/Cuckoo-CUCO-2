import React from 'react'
import {NavLink, useHistory} from 'react-router-dom'
import {ChevronRightIcon} from '@chakra-ui/icons'
import {useWindowSize} from '@hooks/useWindowSize'
import classes from './Menu.module.css'
import {privateRoutes} from '@app/Routes'
import {authApi} from '@app/api'
import {Icons} from '../../UI/iconComponents'
import {NavItem} from '@app/theme'
import {entityAPI, managerAPI} from '@app/services'
import {workersAPI} from '@app/services/workerService'
import {useCurrentUser} from '@hooks/useCurrentManager'
import {Avatar, Box, BoxProps, CloseButton, Flex, HStack, List, ListIcon, Stack, Text, VStack} from '@chakra-ui/react'


interface IMenuContentProps extends BoxProps {
  onClose: () => void
}

interface ICount {
  entities: number | null
  workers: number | null
  managers: number | null
}

const dataCount = (path: string, count: ICount): number | undefined => {
  switch (path) {
    case '/entities':
      return count.entities
    case '/workers':
      return count.workers
    case '/managers':
      return count.managers
    default:
      return null
  }
}

export const MenuMobileContent: React.FC<IMenuContentProps> = (props) => {
  const {data: entities} = entityAPI.useFetchAllEntitiesQuery(1)
  const {data: workers} = workersAPI.useFetchAllWorkersQuery(1)
  const {data: managers} = managerAPI.useFetchAllManagersQuery(1)
  const {isMobile} = useWindowSize()
  const history = useHistory()
  const ROUTE_NAMES = ['Объекты', 'Работники', 'Менеджеры']

  const counts: ICount = {
    entities: entities?.length,
    workers: workers?.length,
    managers: managers?.length,
  }

  const {onClose, ...rest} = props
  const logoutHandler = () => {
    authApi.logout()
  }

  const {manager: currentUser} = useCurrentUser()

  if (isMobile) {
    // @ts-ignore
    return (
      <Box
        w="full"
        pos="fixed"
        h="full"
        // {...rest}
        className={classes.Menu}
        py={7}
      >
        <Flex justifyContent='space-between' ml={7} mr={7} mb={7}>


        {/* <HStack as={NavLink} to="/profile" className={classes.Profile}>
          <Avatar
            size="md"
            fontSize="sm"
            name={`${currentUser?.name} ${currentUser?.surname} ${currentUser?.patronymic}`}
          />
          <Text color="white" fontSize="sm" className={classes.UserName}>
            {currentUser?.name} {currentUser?.surname} {currentUser?.patronymic}
          </Text>
          <ChevronRightIcon/>
        </HStack> */}
        <Stack  mb={3}>
          <Avatar
            size="md"
            fontSize="sm"
            name={`${currentUser?.name} ${currentUser?.surname} ${currentUser?.patronymic}`}
          />

          <Text color="white" fontSize="14px" fontWeight="700" className={classes.UserName}>
            {currentUser?.name} {currentUser?.surname} {currentUser?.patronymic}
          </Text>
           <HStack>
           <Text as='button' onClick={() => (history.push('/profile'),onClose())} fontSize="12px" color="#8C8C8C" fontWeight="400">
              Настройки профиля
            </Text>
           </HStack>

        </Stack>
        <CloseButton color={'white'} colorScheme="gray" display="flex" onClick={onClose} />
        </Flex>
        <List as="nav" d="flex" flexGrow={1} flexDir="column">
          {privateRoutes
            .filter(
              (link, index) => link.label !== 'Объекты' && link.label !== 'Работники' && ( link.label !== "Сотрудники") && link.label !== 'События'
            )
            .map(
              (link, index) =>
                link.isShowMenu && (
                  <NavItem
                    as={NavLink}
                    onClick={()=> (history.push(link.path),onClose())}
                    to={link.path}
                    exact={link.exact}
                    className={index < 3 ? classes.listItem : classes.listItemInOverlay}
                    activeClassName={classes.active}
                    key={index}
                  >
                    <div className={classes.listItemLabel}>
                      <ListIcon as={link.icon} color="gray" />
                      {link.label}
                    </div>
                    {/*<span>{dataCount(link.path, counts)}</span>*/}
                  </NavItem>
                )
            )}
          <NavItem as={NavLink} to="/" className={classes.exit} exact onClick={logoutHandler}>
            <ListIcon className={classes.listItemLabel} as={Icons.IconMenuLogout} color="#878787" />
            Выход
          </NavItem>
        </List>
      </Box>
    )
  }
  return <></>
}
