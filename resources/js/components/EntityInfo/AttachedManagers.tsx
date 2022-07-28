import React, { useState } from 'react'
import {Avatar, Flex, HStack, VStack, Text, Box, Wrap} from '@chakra-ui/react'
import UnAttachManagerModal from '@components/EntityInfo/UnAttachManagerModal'
import {Icons} from '@components/UI/iconComponents'
import {IManager} from '@models/manager'
import {Link as ExternalLink} from '@chakra-ui/react'
import { getFullName } from '@app/helpers'
import { maskPhone } from '@app/helpers/maskPhone'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'

interface IAttachedManagersProps {
  manager: IManager

}

const AttachedManagers: React.FC<IAttachedManagersProps> = ({manager},props) => {
  const {name, surname,patronymic, id, phone} = manager
  const {isEdit} = usePermissions(ROUTE_NAMES.objects)
const {colors} = props

  return (
    <Box
      w={"100%"}
      h='93px'
      px={6}
      py={5}
      // my={2}
      border="1px solid #D8D8D8"
      borderColor={colors}
    >
      <Flex justifyContent="space-between" w="full">
        <HStack>
          <Avatar size="sm" name={`${surname} ${name}`} />
          <Text fontWeight="bold">{getFullName(surname, name, patronymic)}</Text>
        </HStack>
        { isEdit && <Box>
          <UnAttachManagerModal id={id}/>
        </Box>}
      </Flex>
      <HStack mt="0.5rem" wrap={'nowrap'}>
        <ExternalLink  href={`tel:${phone}`}>
          <Icons.IconPhone mr={2} />
          {maskPhone(phone)}
        </ExternalLink>
      </HStack>
    </Box>
  )
}

export default AttachedManagers
