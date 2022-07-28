import React from 'react'
import { MenuMobileContent } from './MenuMobileContent'
import { NavLink } from 'react-router-dom'
import {
  Avatar,
  Box,
  BoxProps,
  CloseButton,
  Drawer,
  DrawerContent,
  Flex,
  FlexProps,
  HStack,
  List,
  ListIcon,
  Text,
  useDisclosure,
  VStack,
} from '@chakra-ui/react'
import { ChevronRightIcon } from '@chakra-ui/icons'
import { useWindowSize } from '@hooks/useWindowSize'
import classes from './Menu.module.css'
import { privateRoutes, ROUTE_NAMES } from '@app/Routes'
import { authApi } from '@app/api'
import { Icons } from '../../UI/iconComponents'
import { NavItem } from '@app/theme'
import { entityAPI, managerAPI } from '@app/services'
import { workersAPI } from '@app/services/workerService'
import { useCurrentUser } from '@hooks/useCurrentManager'


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

const MenuContent: React.FC<IMenuContentProps> = (props) => {
  const { data: entities } = entityAPI.useFetchAllEntitiesQuery(1)
  const { data: workers } = workersAPI.useFetchAllWorkersQuery(1)
  const { data: managers } = managerAPI.useFetchAllManagersQuery(1)
  const { isMobile } = useWindowSize()

  const counts: ICount = {
    entities: entities?.length,
    workers: workers?.length,
    managers: managers?.length,
  }

  const { onClose, ...rest } = props
  const logoutHandler = () => {
    authApi.logout()
  }

  const { manager: currentUser } = useCurrentUser()

  if (!isMobile) {
    return (
      <Box

        w={{ base: 'none', md: '240px', '2xl': 80 }}
        pos="fixed"
        h="full"
        {...rest}
        className={classes.Menu}
        py={7}
      >
        <HStack justifyContent="space-between" ml={7} mr={7} mb={7}>
          <Icons.IconLogo w={40} h={12} color="blue"/>
          <CloseButton colorScheme="gray" display={{ base: 'flex', md: 'none' }}
                       onClick={onClose}/>
        </HStack>
        <HStack as={NavLink} to="/profile" className={classes.Profile}>
          <Avatar
            size="md"
            fontSize="sm"
            name={`${currentUser?.name} ${currentUser?.surname} ${currentUser?.patronymic}`}
          />
          <Text color="white" fontSize="sm" className={classes.UserName}>
            {currentUser?.name} {currentUser?.surname} {currentUser?.patronymic}
          </Text>
          <ChevronRightIcon/>
        </HStack>

        <List as="nav" d="flex" flexGrow={1} flexDir="column">
          {privateRoutes.map(
            (link, index) =>
              link.isShowMenu && (
                <NavItem
                  as={NavLink}
                  to={link.path}
                  exact={link.exact}
                  className={classes.listItem}
                  activeClassName={classes.active}
                  key={index}
                >
                  <div className={classes.listItemLabel}>
                    <ListIcon as={link.icon} color="gray"/>
                    {link.label}
                  </div>
                  <span>{dataCount(link.path, counts)}</span>
                </NavItem>
              ),
          )}
          <NavItem as={NavLink} to="/" className={classes.exit} exact
                   onClick={logoutHandler}>
            <ListIcon className={classes.listItemLabel}
                      as={Icons.IconMenuLogout} color="#878787"/>
            Выход
          </NavItem>
        </List>
      </Box>
    )
  }
  return <></>
}


interface MobileProps extends FlexProps {
  onOpen: () => void
}


const MobileMenu: React.FC<MobileProps> = (props) => {
  const { onOpen, } = props
  const { isMobile } = useWindowSize()

  if (isMobile) {
    return (
      <Flex
        className={classes.MenuMobile}
      >

        <List as="nav" d="flex" flexGrow={1} flexDir="row">
          {privateRoutes?.filter(
            (link) =>
              link.label !== 'Отчеты' &&
              link.label !== 'Лог' &&
              link.label !== 'Настройки доступа' &&
              link.isShowMenu,
          ).sort((link1, link2) => {
            if (link1.mobileQueueNum && link2.mobileQueueNum ) {
              return link1.mobileQueueNum - link2.mobileQueueNum
            }
            return 0
          }).map(
            (link, index) => {
              if (link.routeName === ROUTE_NAMES.managers) {
                link.label = "Сотрудники"
              }
              return link.isShowMenu && (
                <NavItem
                  as={NavLink}
                  to={link.path}
                  exact={link.exact}
                  className={classes.listItemMobile}
                  activeClassName={classes.activeMobile}
                  key={index}
                >
                  <VStack>
                    <ListIcon as={link.icon} color="gray"/>
                    <Text fontSize="10px" color="#8C8C8C">
                      {link.label}
                    </Text>
                  </VStack>
                </NavItem>
              )
            },
          )}
          <NavItem>
            <VStack>
              <Box as="button" onClick={onOpen}>
                <ListIcon as={Icons.IconHorizonalDots} color="gray"/>
                <Text className={classes.listItem} fontSize="10px"
                      color=" #8C8C8C">
                  Еще
                </Text>
              </Box>
            </VStack>
          </NavItem>
        </List>
      </Flex>
    )
  }
  return <></>
}

// interface MobileProps extends FlexProps {
//   onOpen: () => void
// }

// const MobileNav: React.FC<MobileProps> = (props) => {
//   const { onOpen, ...rest } = props
//   return (
//     <Flex
//       ml={{ base: 0, md: 80 }}
//       px={{ base: 4, md: 24 }}
//       height="20"
//       alignItems="center"
//       bg="white"
//       justifyContent="flex-start"
//       {...rest}
//     >
//       <IconButton variant="outline" onClick={onOpen} aria-label="open menu"
//                   icon={<HamburgerIcon/>}/>

//       <Icons.IconLogo w={40} h={12} color="blue"/>
//     </Flex>
//   )
// }

export const Menu: React.FC = (props) => {
  const { children } = props
  const { isOpen, onOpen, onClose } = useDisclosure()
  return (
    <Box minH="100vh">
      <MenuContent onClose={onClose} display={{ base: 'none', md: 'block' }}/>
      <Drawer
        autoFocus={false}
        isOpen={isOpen}
        placement="left"
        onClose={onClose}
        returnFocusOnClose={false}
        onOverlayClick={onClose}
        size="full"
      >
        <DrawerContent>
          <MenuMobileContent onClose={onClose}/>
        </DrawerContent>
      </Drawer>
      {/* <MobileNav display={{ base: 'none', md: 'none' }} onOpen={onOpen}/> */}
      <Box className={classes.main_content}
           ml={{ base: 0, xl: 60, '2xl': 80 }}>{children}</Box>
      <MobileMenu onOpen={onOpen}/>
    </Box>
  )
}
