import React, { useCallback, useEffect, useState } from 'react'
import { IEvents } from '@models/events'
import { Card } from '@app/theme'
import { Icons } from '@components/UI/iconComponents'
import {
  Box,
  Flex,
  Wrap,
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
  Heading,
  Text,
  MenuList,
  MenuItem,
  MenuButton,
  Menu,
  Button, HStack,
} from '@chakra-ui/react'
import { entityAPI } from '@app/services'

import { Loader } from '@components/UI/Loader'

import { STATUS_OPTION_OBJ } from '@app/helpers/constants/eventFilterKeys'
import { getEventStatus } from '@app/helpers/getEventStatus'
import { getDateAndMonth } from '@app/helpers/getDateAndMonth'
import { EventTable } from '@components/EventTable/EventTable'

import 'react-date-range/dist/styles.css'
import 'react-date-range/dist/theme/default.css'
import '../CustomCalendar/DateRange.css'

import { CustomCalendar } from '@components/CustomCalendar/CustomCalendar'
import { ChevronDownIcon } from '@chakra-ui/icons'
import { isDateInRange } from '@app/helpers/isDateInRange'
import { log } from '@models/post'


interface IEventsListProps {
  events: IEvents[]
  isLoading: boolean
}


const STATUS_OPTION = [
  STATUS_OPTION_OBJ.ALL, STATUS_OPTION_OBJ.CHECKED, STATUS_OPTION_OBJ.FAILED,
]

export const EventList: React.FC<IEventsListProps> = ({
  isLoading,
  events = [],
}) => {

  const {
    data: entities,
    isLoading: isLoadingEntities,
  } = entityAPI.useFetchAllEntitiesQuery(1)


  const [filteredEvents, setFilteredEvents] = useState<IEvents[]>(events)

  const [filterByDate, setFilterByDate] = useState<boolean>(false)
  const [value, setValue] = useState<string>('')
  const [status, setStatus] = useState<string>(STATUS_OPTION_OBJ.ALL)
  const [entity, setEntity] = useState<string>('0')
  const [isOpen, setIsopen] = useState<boolean>(false)
  const [period, setPeriod] = useState<{ startDate: null | number, endDate: null | number }>(
    {
      startDate: null,
      endDate: null,
    })

  const setDateRange = useCallback(
    (startDate: number, endDate: number): void => {
      setPeriod({
        startDate: startDate,
        endDate: endDate,
      })
    }, [period])

  const handleStatusSelect = (e): void => {
    setStatus(e.target.value)
  }

  const handleEntitySelect = (e): void => {
    setEntity(e.target.value)
  }

  filteredEvents.length && getDateAndMonth(filteredEvents)

  // let timerId: NodeJS.Timeout
  useEffect(() => {

    const timerId = setTimeout(() => {
      const filtEvs = events.filter(singleEvent => {
        return (getEventStatus(singleEvent.type) === status || status ===
            STATUS_OPTION_OBJ.ALL)
          && (String(singleEvent?.entity?.id) === entity || entity === '0') &&
          isDateInRange(period.startDate, period.endDate, +singleEvent.date) &&
          (log[singleEvent?.type]).toLowerCase().
            includes(value.toLowerCase().trim())
      })

      setFilteredEvents(filtEvs)
    }, 500)

    return () => {
      clearTimeout(timerId)
    }

  }, [status, entity, filterByDate, value])

  const submitDateHandler = () => {
    setFilterByDate(p => !p)
    setIsopen(false)
  }

  const eventsObject = getDateAndMonth(filteredEvents)

  const cancelDateSelection = () => {
    setPeriod({
      ...period,
      endDate: null,
    })
    setFilterByDate(p => !p)
    setIsopen(false)
  }

  useEffect(() => {
    if (filteredEvents.length == 0) {
      setFilteredEvents(events)
    }
  }, [events.length, filteredEvents])
  
  return (
    <Box>
      <Card mb={4}>
        <Heading as="h4" size="lg" mt="2.5rem">События</Heading>
        <SimpleGrid mb={7} columns={[1, 2]} gap={7} mt="2.5rem">
          <Box>
            <InputGroup>
              <Input
                value={value}
                name="search"
                placeholder="Поиск"
                onChange={(e) => setValue(e.target.value)}
              />
              <InputRightElement>
                <Icons.IconSearch/>
              </InputRightElement>
            </InputGroup>
          </Box>
          <Flex justifyContent={{ base: 'flex-start', md: 'flex-end' }}
                mb="1.85rem">


            <Wrap spacing={4}>
              <Select onChange={handleStatusSelect}
                      w={{ base: 'full', md: '122px' }}
                      defaultValue={'Статус'}
              >

                <option disabled hidden>Статус</option>
                {STATUS_OPTION.map((option, idx) => <option value={option}
                                                            key={idx}>{option}</option>)}
              </Select>


              <Select w={{ base: 'full', md: '122px' }}
                      defaultValue={'Объект'}
                      onChange={handleEntitySelect}>
                <option disabled hidden>Объект</option>
                <option value={0}>Все</option>
                {!isLoadingEntities && entities.map(
                  ent => <option value={ent.id}
                                 key={ent.id}> {ent.name}</option>)}
              </Select>

              <Menu
                isOpen={isOpen}
                closeOnSelect={false}
              >
                <MenuButton
                  w={{ base: 'full', md: '122px' }}
                  h={'37px'}
                  onClick={() => setIsopen(p => !p)}
                  pl={'1em'}
                  pr={'1em'}
                  transition="all 0.2s"
                  borderRadius="md"
                  borderWidth="1px"
                >
                  <HStack justifyContent={'space-between'}>
                    <Text>
                      Период
                    </Text>

                    <ChevronDownIcon/>
                  </HStack>

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

            </Wrap>
          </Flex>
        </SimpleGrid>


        {isLoading ? <Loader/> : <TableContainer>
          <Table size="sm">
            <Thead>
              <Tr>
                <Th border={'none'}>Событие</Th>
                <Th border={'none'}>Работник</Th>
                <Th border={'none'}>Объект</Th>
                <Th border={'none'}>Пост</Th>
                <Th border={'none'}>Менеджер</Th>
                <Th border={'none'}>Дата и время</Th>
              </Tr>
            </Thead>
            <Tbody>


              {
                filteredEvents &&
                Object.keys(eventsObject)?.map((item: string, idx) => (
                  <EventTable key={idx} date={item}
                              eventsObject={eventsObject}/>
                ))
              }


            </Tbody>
          </Table>
        </TableContainer>}
      </Card>

    </Box>
  )
}


