import React, { useCallback, useEffect, useState } from 'react'
import classes from './LogList.module.css'
import { Card } from '@app/theme'
import { Icons } from '@components/UI/iconComponents'
import { logApI } from '@app/services'
import moment from 'moment'
import { Controller, useForm } from 'react-hook-form'
import { FormSelect } from '../UI/FormSelect'
import {
  Box,
  Flex,
  Input,
  InputGroup,
  InputRightElement,
  Select,
  SimpleGrid,
  Table,
  TableContainer,
  Tbody,
  Th,
  Thead,
  Tr,
  Text,
  Td,
  Heading,
  Stack, Menu, MenuButton, MenuList, MenuItem, Button,
} from '@chakra-ui/react'
import { ChevronDownIcon } from '@chakra-ui/icons'
import { CustomCalendar } from '@components/CustomCalendar/CustomCalendar'

import { isDateInRange } from '@app/helpers/isDateInRange'
import { LogTable } from '@components/LogList/logTable'
import { getDateAndMonthLogs } from '@app/helpers/getDateAndMonthLogs'


export const LogList = () => {
  const { data: log } = logApI.useFetchAllLogsQuery(1)

  const [filters, setFilters] = useState(log)
  const [value, setValue] = useState('')

  const [isOpen, setIsOpen] = useState<boolean>(false)

  const [filterByDate, setFilterByDate] = useState<boolean>(false)

  const [period, setPeriod] = useState<{ startDate: null | number, endDate: null | number }>(
    {
      startDate: null,
      endDate: null,
    })


  const logsObject = getDateAndMonthLogs(filters)



  const setDateRange = useCallback(
    (startDate: number, endDate: number): void => {
      setPeriod({
        startDate: startDate,
        endDate: endDate,
      })
    }, [period])

  const submitDateHandler = () => {
    setFilterByDate(p => !p)
    setIsOpen(false)
  }


  const cancelDateSelection = () => {
    setPeriod({
      ...period,
      endDate: null,
    })
    setFilterByDate(p => !p)
    setIsOpen(false)
  }

  useEffect(() => {
    const filtEvs = log.filter(singleLog => {
      return isDateInRange(period.startDate, period.endDate, +singleLog.date) && singleLog.text.toLowerCase().includes(value.toLowerCase().trim())
    })
    setFilters(filtEvs)

  }, [filterByDate, value])





  return (
    <Card mb={4}>
      <Heading as="h4" size="md" mb="2rem">
        Лог
      </Heading>
      <Stack justifyContent="space-between"
             direction={{ base: 'column', md: 'row' }} mb="1.85rem">
        <Box w={{ base: '200', md: '532px' }}>
          <InputGroup>
            <Input name="search" placeholder="Поиск по объектам"
                   onChange={(e) => setValue(e.target.value)}/>
            <InputRightElement>
              <Icons.IconSearch/>
            </InputRightElement>
          </InputGroup>
        </Box>
        <Box>

        </Box>


        <Menu
          isOpen={isOpen}
          closeOnSelect={false}
        >
          <MenuButton
            onClick={() => setIsOpen(p => !p)}
            pl={'1em'}
            pr={'1em'}
            transition="all 0.2s"
            borderRadius="md"
            borderWidth="1px"
          >
            Период
            <ChevronDownIcon/>
          </MenuButton>
          <MenuList>
            <MenuItem w={'full'} p={0}>
              <CustomCalendar handleChange={setDateRange}/>
            </MenuItem>

            <MenuItem
              ml={'8px'}
              mr={'8px'}
              w={'full'}
              p={0}
              _hover={{ bg: 'transparent' }}

            >
              <Flex
                flexDirection={'column'}
                w={'full'}
                ml={'8px'}
                mr={'16px'}
                gridGap={'16px'}
              >
                <Button
                  as={Button}
                  onClick={submitDateHandler}
                  w={'full'}
                  colorScheme="blue"
                >
                  Применить
                </Button>

                <Button
                  as={Button}
                  onClick={cancelDateSelection}
                  w={'full'}
                  color={'#3E74F4'}
                  variant={'outline'}
                >
                  Сбросить
                </Button>

              </Flex>
            </MenuItem>
          </MenuList>
        </Menu>


      </Stack>
      <Box>
        <TableContainer mt="2rem">
          <Table size="sm">
            <Thead>
              <Tr>
                <Th border={'none'}>Менеджер</Th>
                <Th border={'none'}>Действие</Th>
                <Th border={'none'} isNumeric>Дата и время</Th>
              </Tr>
            </Thead>
            <Tbody>

              {
              filters && Object.keys(logsObject)?.map(log => <LogTable key={log} date={log} logsObject={logsObject} />
                )
              }

            </Tbody>
          </Table>
        </TableContainer>
      </Box>
    </Card>
  )
}
