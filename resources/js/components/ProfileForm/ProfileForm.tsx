import React from 'react'

import {Button, SimpleGrid} from '@chakra-ui/react'
import {useForm} from 'react-hook-form'
import {yupResolver} from '@hookform/resolvers/yup'
import * as Yup from 'yup'

import {Form} from '../UI/Form'
import {Input} from '../UI/Input'
import {errors} from '../../errors'

interface IProfileFormProps {
  initialValues?: IProfileFormValues

  submitHandler(data: IProfileFormValues): Promise<void>
}

export interface IProfileFormValues {
  name: string
  surname: string
  patronymic: string
  phone: string
}

const validationSchema = Yup.object().shape({
  name: Yup.string().required(errors.required),
  surname: Yup.string().required(errors.required),
  patronymic: Yup.string().required(errors.required),
  phone: Yup.string()
    .required(errors.required)
    .matches(/^(?:[+\d].*\d|\d)$/, errors.phoneLength)
    .length(16, errors.phoneLength),
})


export const ProfileForm: React.FC<IProfileFormProps> = (props) => {
  const {submitHandler, initialValues} = props

  const defaultValues = {
    name: initialValues.name,
    surname: initialValues.surname,
    patronymic: initialValues.patronymic,
    phone: initialValues.phone,
  }

  const {
    register,
    handleSubmit,
    formState: {errors, isSubmitting},
    control,
  } = useForm<IProfileFormValues>({
    defaultValues: initialValues || defaultValues,
    resolver: yupResolver(validationSchema),
  })

  return (
    <Form onSubmit={handleSubmit(submitHandler)}>
      <SimpleGrid columns={3} spacingX={7} spacingY={6} mb={6}>
        <Input {...register('name')} error={!!errors.name} helperText={errors?.name?.message} label="Имя" />
        <Input
          {...register('surname')}
          error={!!errors.surname}
          helperText={errors?.surname?.message}
          label="Фамилия"
        />
        <Input
          {...register('patronymic')}
          error={!!errors.patronymic}
          helperText={errors?.patronymic?.message}
          label="Отчество"
        />
        <Input
          {...register('phone')}
          error={!!errors.phone}
          type="tel"
          control={control}
          helperText={errors?.phone?.message}
          label="Телефон"
        />
      </SimpleGrid>
      <Button isLoading={isSubmitting} type="submit" colorScheme="green" variant="solid">
        Изменить данные
      </Button>
    </Form>
  )
}
