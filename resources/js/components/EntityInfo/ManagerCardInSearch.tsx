import React, {useMemo} from 'react'
import {Avatar, Box, Flex, HStack, Link as ExternalLink, Text, Wrap} from '@chakra-ui/react'
import {IManager} from '@models/manager'
import {getFullName} from '@app/helpers'
import {Icons} from '@components/UI/iconComponents'
import {maskPhone} from '@app/helpers/maskPhone'
import {normalizeStatusData} from '@app/helpers/normalizeStatusData'
import {entityCountWordInRussian} from '@app/helpers/wordList'

export const ManagerCardInSearch: React.FC<IManager> = (props) => {
  const {name, entities, surname, patronymic, phone, status} = props

  const normalizedStatus = useMemo(() => normalizeStatusData(status, true), [status])

  return (
    <Box

      w={{base: 'full', md: '415px'}}
      h="auto"
      px={6}
      py={5}
      my={2}
      pb={'16px'}
      pt={'16px'}
      mt={0}
      mb={0}
    >

      <Flex justifyContent="space-between" w="full">
        <HStack>
          <Avatar size="sm" name={`${surname} ${name}`} />
          <Text fontWeight="bold" fontSize={{base:'12px' , md:'14px'}}>{getFullName(surname, name, patronymic)}</Text>
        </HStack>

        <Flex justifyContent='flex-end'>
          <Text fontSize={{base:'12px',md:'14px'}}>{normalizedStatus}</Text>
        </Flex>

      </Flex>
      <HStack wrap={'wrap'} spacing={9}>
        <Wrap mt="0.5rem">
          <ExternalLink href={`tel:${phone}`}>
            <Icons.IconPhone mr={2} />
            {maskPhone(phone)}
          </ExternalLink>
        </Wrap>
        <HStack spacing={2}>
          <Icons.IconKey color="iconGray" h="17px" w="14px" />
          <Text fontSize='12px'>
            {entities.length} {entityCountWordInRussian(entities.length)}
          </Text>
        </HStack>
      </HStack>
    </Box>

  )
}

export default ManagerCardInSearch
