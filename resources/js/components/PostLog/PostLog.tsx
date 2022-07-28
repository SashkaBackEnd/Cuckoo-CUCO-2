import React from 'react'

import { Collapse, Flex, Text, useDisclosure } from '@chakra-ui/react'
import moment from 'moment'
import { Divider, HStack } from '@chakra-ui/layout'
import { ChevronDownIcon, ChevronUpIcon } from '@chakra-ui/icons'

import { Icons } from '../UI/iconComponents'
import { ILog, log } from '@models/post'
import { BoxDivider } from '../UI/BoxDivider'
import { getLogColor } from '@app/helpers'


interface IPostLogProps {
  log: ILog[]
}

const ITEM_HEIGHT = 56

const LogItem: React.FC<ILog> = (props) => {
  const {type, date, securityGuard} = props

  return (
    <>
      <HStack justify="space-between" spacing={2} py={3} >
        <HStack spacing={3}>
          <Icons.IconCircle w={3} h={3} color={getLogColor(type)} />
          <Text height={"auto"}>
            {securityGuard && securityGuard.shortName} {log[type] || type}
          </Text>
        </HStack>
        <Text color="gray">{moment(date * 1000).format('HH:mm')}</Text>
      </HStack>
      <Divider m={0} />
    </>
  )
}

export const PostLog: React.FC<IPostLogProps> = (props) => {
  const {log} = props
  const {isOpen, onToggle} = useDisclosure()


  return (
    <>
    <BoxDivider>События за последние 24 часа</BoxDivider>
      {log.length ? (
        <>
          <Flex
            as={Collapse}
            in={isOpen}
            startingHeight={log.length > 4 ? ITEM_HEIGHT * 4 : ITEM_HEIGHT * log.length}
            animateOpacity
            flexDir="column"
          >
            {log.map((log) => (
              <LogItem key={log.id} {...log} />
            ))}
          </Flex>
          {log.length > 4 && (
            <Text mt={4} fontWeight="bold" _hover={{cursor: 'pointer', color: 'darkgray'}} onClick={onToggle}>
              {isOpen ? 'Скрыть' : 'Показать все события'}
              {isOpen ? <ChevronUpIcon /> : <ChevronDownIcon />}
            </Text>
          )}
        </>
      ) : (
        <Text textAlign="center">Событий нет</Text>
      )}
    </>
  )
}
