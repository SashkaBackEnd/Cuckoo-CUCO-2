import React from 'react'

import {Button, HStack} from '@chakra-ui/react'
import {useForm} from 'react-hook-form'
import {yupResolver} from '@hookform/resolvers/yup'
import * as Yup from 'yup'

import {Input} from '../UI/Input'
import {Form} from '../UI/Form'
import {errors} from '../../errors'
import {Heading} from '@chakra-ui/layout'

interface ILoginFormProps {
  initialValues?: ILoginFormValues

  submitHandler(data: ILoginFormValues): Promise<void>
}

export interface ILoginFormValues {
  oldLogin: string
  login: string
}

const validationSchema = Yup.object().shape({
  oldLogin: Yup.string().required(errors.required),
  login: Yup.string().required(errors.required),
})
const defaultValues = {
  oldLogin: '',
  login: '',
}

export const LoginForm: React.FC<ILoginFormProps> = (props) => {
  const {submitHandler, initialValues} = props

  const {
    register,
    handleSubmit,
    formState: {errors, isSubmitting},
  } = useForm<ILoginFormValues>({
    defaultValues: initialValues || defaultValues,
    resolver: yupResolver(validationSchema),
  })

  return (
    <Form w="360px" onSubmit={handleSubmit(submitHandler)}>
      <Heading as="h4" size="md" mb={6}>
        Изменить логин
      </Heading>
      <HStack mb={6}>
        <Input
          {...register('oldLogin')}
          error={!!errors.oldLogin}
          helperText={errors?.oldLogin?.message}
          label="Старый логин"
        />
      </HStack>
      <HStack mb={6}>
        <Input {...register('login')} error={!!errors.login} helperText={errors?.login?.message} label="Новый логин" />
      </HStack>
      <Button isLoading={isSubmitting} type="submit" colorScheme="green" variant="solid">
        Изменить логин
      </Button>
    </Form>
  )
}
