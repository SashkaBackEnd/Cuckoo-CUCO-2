import React from 'react'

import {
  Button,
  forwardRef,
  Modal,
  ModalBody,
  ModalCloseButton,
  ModalContent,
  ModalFooter,
  ModalHeader,
  ModalOverlay,
  ThemingProps,
  useBoolean,
  useDisclosure,
} from '@chakra-ui/react'

import classes from './DeleteButton.module.css'

interface IDeleteButtonProps extends ThemingProps {
  title: string
  description?: string
  className?: string
  modalSize?: 'sm' | 'lg' | 'xl'

  deleteFn(): Promise<void>
}

export const DeleteButton: React.FC<IDeleteButtonProps> = forwardRef((props, ref) => {
  const {deleteFn, title, description, className, modalSize = 'lg', children, ...rest} = props
  const {isOpen, onOpen, onClose} = useDisclosure()
  const [isLoading, setIsLoading] = useBoolean()

  const onClick = () => {
    setIsLoading.on()
    deleteFn()
      .then()
      .finally(() => {
        setIsLoading.off()
        onClose()
      })
  }

  return (
    <>
      <Button {...rest} ref={ref} onClick={onOpen} type="button" className={className}>
        {children}
      </Button>

      <Modal isOpen={isOpen} onClose={onClose} size={modalSize}>
        <ModalOverlay />
        <ModalContent>
          <ModalHeader>{title}</ModalHeader>
          <ModalCloseButton />
          <ModalBody>{description && <p className={classes.description}>{description}</p>}</ModalBody>
          <ModalFooter justifyContent="space-between">
            <Button isLoading={isLoading} onClick={onClick} colorScheme="red">
              Удалить
            </Button>
            <Button colorScheme="gray" onClick={onClose}>
              Отмена
            </Button>
          </ModalFooter>
        </ModalContent>
      </Modal>
    </>
  )
})
