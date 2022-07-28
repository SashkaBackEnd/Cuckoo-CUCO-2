import React from 'react'
import {Avatar, Box, Button, ButtonGroup, Heading, HStack, Input, InputGroup, InputRightElement, Text, VStack} from '@chakra-ui/react'
import { Card } from '@app/theme'
import { Icons } from '@components/UI/iconComponents'
import { MobileAccess } from './MobileAccess'
import { Loader } from '@components/UI/Loader'
import { managerAPI } from '@app/services'


interface IAccessSettingsProps {}

 export const MobileSetings: React.FC<IAccessSettingsProps>  = () => {
  const {data: managers, isLoading} = managerAPI.useFetchAllManagersQuery(1)

  if (isLoading) {
    return <Loader />
  }

  
  return (
    <Card>
      <Heading as="h4" size="md" mb="2rem">
        Настройки доступа
      </Heading>
      <Box>
        <InputGroup mt={8}>
          <Input name="search" placeholder="Поиск" />
          <InputRightElement>
            <Icons.IconSearch />
          </InputRightElement>
        </InputGroup>
      </Box>
      <Box backgroundColor="#F5F5F5" w='full' height={8} mt={8}>
        <Text pt={1} pl={2}>
          Сотрудники
        </Text>
      </Box>
      {managers.map((manager, idx) => {
            return <MobileAccess key={manager.id} idx={idx} manager={manager} />
          })}
    </Card>
  )
}

