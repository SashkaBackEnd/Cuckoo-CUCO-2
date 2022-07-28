import React from 'react'

import {Button, IconButton, InputGroup, InputRightElement, useBoolean} from '@chakra-ui/react'
import {useForm} from 'react-hook-form'
import {yupResolver} from '@hookform/resolvers/yup'
import * as Yup from 'yup'

import {Input} from '../UI/Input'
import {Form} from '../UI/Form'
import {errors} from '../../errors'
import {ViewIcon, ViewOffIcon} from '@chakra-ui/icons'
import {Heading} from '@chakra-ui/layout'

interface IPasswordFormProps {
  initialValues?: IPasswordFormValues

  submitHandler(data: IPasswordFormValues): Promise<void>
}

export interface IPasswordFormValues {
  oldPassword: string
  password: string
}

const validationSchema = Yup.object().shape({
  oldPassword: Yup.string().required(errors.required),
  password: Yup.string().required(errors.required),
})
const defaultValues = {
  oldPassword: '',
  password: '',
}

export const PasswordForm: React.FC<IPasswordFormProps> = (props) => {
  const {submitHandler, initialValues} = props
  const [showOld, setShowOld] = useBoolean()
  const [show, setShow] = useBoolean()

  const {
    register,
    handleSubmit,
    formState: {errors, isSubmitting},
  } = useForm<IPasswordFormValues>({
    defaultValues: initialValues || defaultValues,
    resolver: yupResolver(validationSchema),
  })

  return (
    <Form w="360px" onSubmit={handleSubmit(submitHandler)}>
      <Heading as="h4" size="md" mb={6}>
        Изменить пароль
      </Heading>
      <InputGroup mb={6}>
        <Input
          {...register('oldPassword')}
          error={!!errors.oldPassword}
          helperText={errors?.oldPassword?.message}
          type={showOld ? 'text' : 'password'}
          label="Старый пароль"
        />
        <InputRightElement h="100%">
          <IconButton
            mt="auto"
            size="sm"
            mb={2}
            variant="ghost"
            onClick={() => setShowOld.toggle()}
            icon={showOld ? <ViewOffIcon /> : <ViewIcon />}
            aria-label={showOld ? 'Скрыть' : 'Показать'}
          />
        </InputRightElement>
      </InputGroup>
      <InputGroup mb={6}>
        <Input
          {...register('password')}
          error={!!errors.password}
          helperText={errors?.password?.message}
          type={show ? 'text' : 'password'}
          label="Новый пароль"
        />
        <InputRightElement h="100%">
          <IconButton
            mt="auto"
            size="sm"
            mb={2}
            variant="ghost"
            onClick={() => setShow.toggle()}
            icon={show ? <ViewOffIcon /> : <ViewIcon />}
            aria-label={show ? 'Скрыть' : 'Показать'}
          />
        </InputRightElement>
      </InputGroup>
      <Button isLoading={isSubmitting} type="submit" colorScheme="green" variant="solid">
        Изменить пароль
      </Button>
    </Form>
  )
}
