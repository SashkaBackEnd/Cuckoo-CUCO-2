import React, {useEffect, useState} from 'react'
import {Icons} from '@components/UI/iconComponents'
import {managerAPI} from '@app/services'
import {IManager} from '@models/manager'
import ManagerCardInSearch from '@components/EntityInfo/ManagerCardInSearch'
import {useParams} from 'react-router-dom'
import {toast} from '@app/theme'
import classes from './EntityInfo.module.css'
import {Form} from '@components/UI/Form'
import {MANAGER_STATUS} from '@app/helpers/constants/managerStatuses'
import {
  Button,
  FormControl,
  Input,
  InputGroup,
  InputRightElement,
  Modal,
  ModalBody,
  ModalCloseButton,
  ModalContent,
  ModalFooter,
  ModalHeader,
  Box,
  ModalOverlay,
  Text,
  useDisclosure,
  VStack,
} from '@chakra-ui/react'

const AttachManagerModal: React.FC = () => {
  const {isOpen, onOpen, onClose} = useDisclosure()

  const {data: managers} = managerAPI.useFetchAllManagersQuery(1)
  const [attachManager, {isLoading, error, isError}] = managerAPI.useAttachManagerMutation()
  const {entityId}: never = useParams()

  const [filteredManagers, setFilteredManagers] = useState<IManager[]>(managers)
  const [filterVal, setFilterVal] = useState<string>('')
  const [selectedManager, setSelectedManager] = useState<IManager>()
  const [ref, setRef] = useState<boolean>(false)


  const handleSelectManager = (manager: IManager): void => {
    // setColors('blue')
    setSelectedManager(manager)
  }

  const initialRef = React.useRef()
  const finalRef = React.useRef()

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>): void => {
    setFilterVal(e.target.value)
  }

  let id;
  useEffect(() => {
    clearTimeout(id)
    id = setTimeout(() => {
      const filtered = managers?.filter((m) => {
        return (
          `${m.name} ${m.surname} ${m.patronymic}`.toLowerCase().includes(filterVal.toLowerCase()) &&
          m.status !== MANAGER_STATUS.deactivated
        )
      })
      setFilteredManagers(filtered)

    }, 500)
  }, [filterVal])

  const handleSubmit = (): void => {
    if (!selectedManager) {
      toast({
        status: 'error',
        title: 'Выберите менеджера',
      })
      return
    }

    attachManager({entity_id: entityId, user_id: selectedManager.id})
      .unwrap()
      .then((res) => {
        toast({
          title: 'Менеджер прикреплен к объекту',
        })
        onClose()
        setFilterVal('')
        setSelectedManager(null)
      })
      .catch((e) => {
        if (e.status === 409) {
          toast({
            status: 'error',
            title: 'Этот менеджер уже прикреплен',
            description: e.data.status,
          })
        } else {
          toast({
            status: 'error',
            title: 'Упс, что-то пошло не так',
          })
        }
      })
      .finally(() => {
        setRef((p) => !p)
      })
  }


  return (
    <>
      <Button onClick={onOpen} mt={0} px={0} leftIcon={<Icons.IconPlus />} colorScheme="grey" variant="ghost">
        Добавить
      </Button>

      <Modal isCentered={true} size={'lg'} initialFocusRef={initialRef} finalFocusRef={finalRef} isOpen={isOpen} onClose={onClose} >
        <ModalOverlay />
        <ModalContent h='570px'   >
          <ModalHeader>Добавить менеджера</ModalHeader>
          <ModalCloseButton color={'#D8D8D8'} />
          <ModalBody pb={6} style={{overflowY:'scroll'}}>
            <FormControl>
              {/* <HStack
              >
                {selectedManager && <ManagerCardInSearch {...selectedManager}/>}
              </HStack> */}

              <Form pb={'8px'}>
                <InputGroup>
                  <Input onChange={handleChange} value={filterVal} ref={initialRef} placeholder="Поиск" />
                  <InputRightElement>
                    <Icons.IconSearch />
                  </InputRightElement>
                </InputGroup>
              </Form>

              <Box
                mt={0}

                // maxW='full'
                w='full'
                // zIndex={100}

                className={classes.ManagerList}
              >
                <VStack mt={2} gridGap={0} p={0} zIndex={'2'} w='full' alignItems={'start'}>
                  {filteredManagers?.length ? (
                    filteredManagers?.map((man) => (
                      <Box

                        border={`1px solid ${ selectedManager?.id === man.id ? 'blue' : '#D8D8D8'}`}
                        height={'auto'}
                        as="button"
                        cursor="pointer"
                        w='full'
                        key={man.id}
                        onClick={() => handleSelectManager(man)}
                      >
                        <ManagerCardInSearch  {...man}  />
                      </Box>
                    ))
                  ) : (
                    <Text> Нет свободных или активных менеджеров </Text>
                  )}
                </VStack>
              </Box>
            </FormControl>
          </ModalBody>

          <ModalFooter justifyContent="space-between" alignItems="center" gridGap={2}>
            <Button isLoading={isLoading} onClick={handleSubmit} colorScheme="blue" w="full">
              Добавить
            </Button>
            <Button onClick={onClose} color="#EDEDED" w="full">
              Отмена
            </Button>
          </ModalFooter>
        </ModalContent>
      </Modal>
    </>
  )
}

export default AttachManagerModal
