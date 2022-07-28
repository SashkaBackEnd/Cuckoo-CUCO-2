import React, { useMemo } from 'react'
import {
  Accordion,
  AccordionButton,
  AccordionIcon,
  AccordionItem,
  AccordionPanel,
  Avatar,
  Box,
  Flex,
  HStack,
  Text,
  VStack,
} from '@chakra-ui/react'
import { Icons } from '@components/UI/iconComponents'
import { IReportsByManagers } from '@models/reports'
import ReportItemDetails from '@components/ReportsList/ReportItemDetails'
import { Circle } from '@chakra-ui/layout'
import { TYPES_FETCH } from '@pages/ReportsPage/ReportsPage'
import { getShiftWordInRussian } from '@app/helpers/getShiftWordInRussian'


interface IReportItemProps {
  report: IReportsByManagers
  type?: string | number
}


const ReportItem: React.FC<IReportItemProps> = ({ report, type }) => {
  const {
    caseObjectGuardMismatch,
    caseShiftTimeExceed,
    caseShirtError,
    shifts,
    caseMissed,
    totalErrors,
    totalCalls,
    salary,
    name,
  } = report

  return (
    <Box mt={3} bg={'white'} p={'24px 40px 0px 40px'}>
      <Accordion allowToggle pb={2}>
        <AccordionItem border={'none'}>
          <Text fontWeight="700" fontSize="14px" w="full">
            <AccordionButton _focus={{ outline: 'transparent' }}
                             border={'none'} p={'9px 16px'}>

              <VStack width={'full'}>

                <HStack bg={'#F5F5F5'} w={'full'} p={'9px 16px'}>
                  <Flex h="32px" w="32px" bg="#A8CAE2" borderRadius="50%"
                        alignItems="flex-start" justifyContent="center">


                    {
                      (type === TYPES_FETCH[1] || type === TYPES_FETCH[3]) &&
                      <Avatar size="sm" h={'32px'} w={'32px'} name={name}/>
                    }

                    {
                      type === TYPES_FETCH[2] &&
                      <Circle size="36px" bg="red.300" color="white">
                        <Icons.IconShield w={4} h={6}/>
                      </Circle>
                    }


                  </Flex>
                  <Box fontWeight={700} fontSize={'14px'} flex="1" ml={3}
                       textAlign="left">
                    {name}
                  </Box>
                  <Box textAlign="right" mr="1.8rem">
                    <HStack>
                      <Text color={'#878787'} fontSize="14px" fontWeight="400">
                        {shifts.length} {getShiftWordInRussian(shifts.length)}
                      </Text>

                      <Text fontSize="14px" fontWeight="700">
                        {salary} ₽
                      </Text>


                    </HStack>
                  </Box>
                  <AccordionIcon/>

                </HStack>


                <HStack pb={'32px'} pl={"20px"} mt={'7px'}
                        w={'full'} overflowX={'auto'}
                        spacing={'28px'} alignItems={'flex-start'}
                        borderBottom={'1px solid #ECECEC'}>
                  <VStack whiteSpace={"nowrap"}
                          minWidth={'87.5px'} alignItems={'flex-start'}
                          justifyContent={'space-between'}>
                    <Text fontSize="12px" color={'#8C8C8C'}
                          fontWeight={400}>Звонков</Text>
                    <HStack m={0}><Icons.IconPhone h={'16px'} w={'16px'}
                                                   color="blue"/><Text>{totalCalls}</Text>
                    </HStack>
                  </VStack>

                  <VStack whiteSpace={"nowrap"} minWidth={'87.5px'}
                          justifyContent={'space-between'} alignItems={'flex-start'}
                    // borderRight={'1px solid #E8E8E8'}
                  >

                    <Text whiteSpace={"nowrap"} fontSize="12px" color={'#8C8C8C'}
                          fontWeight={400}>Ошибки</Text>
                    <HStack><Icons.IconErrrors h={'16px'} w={'16px'}
                                               color="white"
                                               bg="white"/><Text>{totalErrors}</Text>
                    </HStack>
                  </VStack>


                  <VStack whiteSpace={"nowrap"} justifyContent={'space-between'}
                          alignItems={'flex-start'}>
                    <Text fontSize="12px" color={'#8C8C8C'}
                          fontWeight={400}>Ошибки при дозвоне</Text>
                    <HStack><Icons.IconEllips
                      h={'8px'} w={'8px'}
                      color="red"/><Text>{caseMissed}</Text>
                    </HStack>
                  </VStack>

                  <VStack whiteSpace={"nowrap"} justifyContent={'space-between'}
                          alignItems={'flex-start'}>
                    <Text fontSize="12px" color={'#8C8C8C'}
                          fontWeight={400}>Ошибок заступления на
                      смену</Text>
                    <HStack><Icons.IconEllips
                      h={'8px'} w={'8px'}
                      color="blue"/><Text>{caseShirtError}</Text>
                    </HStack>
                  </VStack>

                  <VStack whiteSpace={"nowrap"} justifyContent={'space-between'}
                          alignItems={'flex-start'}>
                    <Text fontSize="12px" color={'#8C8C8C'}
                          fontWeight={400}>Ошибка повторного
                      заступления</Text>
                    <HStack><Icons.IconEllips
                      h={'8px'} w={'8px'}
                      color="blue"/><Text>{caseObjectGuardMismatch}</Text>
                    </HStack>
                  </VStack>

                  <VStack whiteSpace={"nowrap"} justifyContent={'space-between'}
                          alignItems={'flex-start'}>
                    <Text fontSize="12px" color={'#8C8C8C'}
                          fontWeight={400}>Превышение времени
                      смены</Text>
                    <HStack><Icons.IconEllips
                      h={'8px'} w={'8px'}
                      color="blue"/><Text>{caseShiftTimeExceed}</Text>
                    </HStack>
                  </VStack>


                </HStack>


              </VStack>
            </AccordionButton>


          </Text>
          <AccordionPanel>

            {
              shifts && shifts.map(
                (shift, idx) => !!shift.startTime !! && shift.endTime &&
                  <ReportItemDetails key={idx} type={type} report={shift}/>)
            }

          </AccordionPanel>
        </AccordionItem>
      </Accordion>
    </Box>
  )
}

export default ReportItem
