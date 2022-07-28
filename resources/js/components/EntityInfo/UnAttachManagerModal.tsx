import {
  Button, Modal,
  ModalBody, ModalCloseButton,
  ModalContent, ModalFooter,
  ModalHeader,
  ModalOverlay, Text, useDisclosure,
} from '@chakra-ui/react'
import { Box } from '@chakra-ui/layout'
import { Icons } from '@components/UI/iconComponents'
import React from 'react'
import { managerAPI } from '@app/services'
import { useParams } from 'react-router-dom'
import { errorHandler } from '@app/errors'
import { toast } from '@app/theme'

interface IUnAttachManagerModalProps {
  id: string
}
const UnAttachManagerModal: React.FC<IUnAttachManagerModalProps> = ({id}) =>  {
  const { isOpen, onOpen, onClose } = useDisclosure()

  const [unnAttachManager, {isLoading, error}] = managerAPI.useUnAttachManagerMutation()
  const {entityId}: never = useParams()

  const unAttachHandler = () => {
    unnAttachManager({entity_id: entityId, user_id: id  })
      .then(() => {
        toast({
          title: "Менеджер откреплен!"
        })
        onClose()
    })
      .catch(e => {
        errorHandler(e)
    })
  }
  return (
    <>
      <Box
        onClick={onOpen}
        cursor="pointer">
        <Icons.IconDelete/>
      </Box>
      <Modal closeOnOverlayClick={true} isOpen={isOpen} onClose={onClose}>
        <ModalOverlay />
        <ModalContent>
          <ModalHeader>Открепить менеджера с объекта?</ModalHeader>
          <ModalCloseButton />
          <ModalBody pb={6}>
            <Text>
              Удалить данного менеджера
            </Text>
          </ModalBody>

          <ModalFooter>
            <Button
              isLoading={isLoading}
              onClick={unAttachHandler}
              colorScheme='blue' mr={3} >
              Открепить
            </Button>

            <Button onClick={onClose} variant='ghost'>Закрыть</Button>
          </ModalFooter>
        </ModalContent>
      </Modal>
    </>
  )
}

export default  UnAttachManagerModal
