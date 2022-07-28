import React from 'react'
import {Input, InputGroup, InputRightElement, Table, Tbody, Td, Text, Th, Thead, Tr} from '@chakra-ui/react'
import {Box, Heading} from '@chakra-ui/layout'
import {managerAPI} from '@app/services'
import {Loader} from '@components/UI/Loader'

import ManagersInAccess from '@components/AccessSettings/ManagerInAccess'
import {Card} from '@app/theme'
import {Icons} from '@components/UI/iconComponents'

interface IAccessSettingsProps {}

export const AccessSettings: React.FC<IAccessSettingsProps> = () => {
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
      <Table variant="simple" mt={8} w='full'>
        <Thead>
          <Tr>
            <Th> ФИО </Th>
            <Th> Объекты </Th>
            <Th> Менеджеры </Th>
            <Th> Работники </Th>
            <Th> Отчеты </Th>
            <Th> Лог </Th>
          </Tr>
        </Thead>

        <Tbody>
          <Tr>
            <Td pl={0} colSpan={6}>
              <Box backgroundColor="#F5F5F5" height={8}>
                <Text pt={1} pl={2}>
                  Сотрудники
                </Text>
              </Box>
            </Td>
          </Tr>
          {managers.map((manager, idx) => {
            return <ManagersInAccess key={manager.id} idx={idx} manager={manager} />
          })}
        </Tbody>
      </Table>
    </Card>
  )
}
