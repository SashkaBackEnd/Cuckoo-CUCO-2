import {
  Avatar, Box,
  Button, Flex, HStack,
  Modal, ModalBody, ModalCloseButton,
  ModalContent, ModalFooter, ModalHeader,
  ModalOverlay, Text,
  useDisclosure, VStack,
} from '@chakra-ui/react'
import React from 'react'
import { Icons } from '@components/UI/iconComponents'
import { IReportsByManagers } from '@models/reports'


interface IModalProps {
  report?: IReportsByManagers
}


export const ReportsListMobileModal: React.FC<IModalProps> = ({report}) => {

  const {
    name,
    shortName,
    object_name,
    shifts,
    id,
    salary,
    totalDoneShifts,
    totalEmergencyCases,
    totalWorkHours,
    totalErrors,
    totalCalls,
    caseObjectGuardMismatch,
    caseMissed,
    caseShirtError,
    caseShiftTimeExceed,
    caseShiftChange,
  } = report
  const { isOpen, onOpen, onClose } = useDisclosure()

  return (
    <>
      <Icons.IconRightArrow onClick={() => {
        onOpen()
      }}
      />

      <Modal isCentered isOpen={isOpen} onClose={onClose}>

        <ModalOverlay bg="rgba(0, 0, 0, 0.6)"/>

        <ModalContent  p={'16px 24px'} w={'100vh'} maxW={'9999px'}>
          <ModalBody p={0}>

            <HStack pb={"20px"}  justifyContent={'space-between'}>
            <HStack >
              <Avatar size="sm"  h={'28px'} w={'28px'} name={name}/>
              <Box fontSize={'12px'} fontWeight={700}> {name} </Box>
            </HStack>

              <HStack spacing={'25px'}>

                <HStack >
                  <Icons.IconKeyGreen/>
                  <Text fontWeight={400}> {totalCalls} </Text>
                </HStack>

                <HStack>
                  <Icons.IconKeyRed/>
                  <Text fontWeight={400}> {totalErrors} </Text>
                </HStack>
            </HStack>





              </HStack>

            <Flex flexWrap={'wrap'} justifyContent={'space-between'} gridGap={'12px'}>
              <Box maxW={'40%'}>
              <Text pb={'8px'} fontWeight={500}> Ошибка дозвоне </Text>
                <HStack>
                  <Icons.IconCircle color={'red'}/>
                  <Text> {caseMissed} </Text>
                </HStack>
              </Box>

              <Box maxW={'40%'} >
                <Text pb={'8px'} fontWeight={500}> Ошибка пересменки </Text>
                <HStack>
                  <Icons.IconCircle color={'red'}/>
                  <Text> {caseShirtError} </Text>
                </HStack>
              </Box>

              <Box maxW={'40%'}>
                <Text pb={'8px'} fontWeight={500}>  Повторное заступление  </Text>
                <HStack>
                  <Icons.IconCircle color={'red'}/>
                  <Text> {caseObjectGuardMismatch} </Text>
                </HStack>
              </Box>

              <Box  pb={'8px'} maxW={'40%'}>
                <Text fontWeight={500}> Перевышение времени смены </Text>
                <HStack>
                  <Icons.IconCircle color={'red'}/>
                  <Text> {caseShiftTimeExceed} </Text>
                </HStack>
              </Box>



            </Flex>

            <VStack mt={'26px'} alignItems={'flex-start'}>
              <Text m={0} fontWeight={500}> Время и зарплата</Text>
              <Text m={0} color={'#878787'} fontWeight={400} fontSize={'10px'}> {totalWorkHours || 0} ч </Text>
              <Text m={0} fontWeight={500}> {salary || 0} ₽</Text>
            </VStack>

          </ModalBody>

        </ModalContent>
      </Modal>
    </>
  )
}
