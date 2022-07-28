import React from 'react'
import { Controller, useForm } from 'react-hook-form'
import { yupResolver } from '@hookform/resolvers/yup'
import { Tab, TabList, TabPanel, TabPanels, Tabs } from '@chakra-ui/tabs'
import { Button, SimpleGrid,Text, Stack, Divider, Box } from '@chakra-ui/react'
import * as Yup from 'yup'
import { Form } from '../UI/Form'
import { Input } from '../UI/Input'
import { errors } from '@app/errors'



interface IManagerFormProps {
  initialValues?: IManagerFormValues
  submitHandler(data: IManagerFormValues): Promise<void>
}


export interface IManagerFormValues {
  name: string
  surname: string
  patronymic: string
  phone: string
  email: string
  oldpassword: string
  newpassword: string
  oldlogin: string
  newlogin?: string
}


const validationSchema = Yup.object().shape({
  name: Yup.string().required(errors.required),
  surname: Yup.string().required(errors.required),
  patronymic: Yup.string().required(errors.required),
  phone: Yup.string().
    required(errors.required).
    matches(/^(?:[+\d].*\d|\d)$/, errors.phoneLength),
    // length(16, errors.phoneLength),
})


const defaultValues: IManagerFormValues = {
  name: '',
  surname: '',
  patronymic: '',
  phone: '',
  email: '',
  oldpassword: '',
  newpassword: '',
  oldlogin: '',
  newlogin: '',
}

export const ManagerForm: React.FC<IManagerFormProps> = (props) => {
  const { initialValues, submitHandler } = props

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
    watch,
    control,
  } = useForm<IManagerFormValues>({
    defaultValues: initialValues || defaultValues,
    resolver: yupResolver(validationSchema),
  })


  return (
    <Form onSubmit={handleSubmit(submitHandler)}>
      <Tabs colorScheme="blue">
        <TabList mb={6}>
          <Tab>Личные данные</Tab>
          <Tab>Настройки учетной записи</Tab>
        </TabList>
        <TabPanels>
          <TabPanel padding={0}>
            <SimpleGrid columns={[1,3]} spacingX={7} spacingY={6} mb={6}>
              <Input
                {...register('name')}
                maxLength={32}
                error={!!errors.name}
                helperText={errors?.name?.message}
                label="Имя"
              />
              <Input
                {...register('surname')}
                maxLength={32}
                error={!!errors.surname}
                helperText={errors?.surname?.message}
                label="Фамилия"
              />
                <Input
                {...register('patronymic')}
                maxLength={32}
                error={!!errors.patronymic}
                helperText={errors?.patronymic?.message}
                label="Отчество"
              />
              <Input
                {...register('phone')}
                error={!!errors.phone}
                helperText={errors?.phone?.message}
                type="tel"
                control={control}
                label="Телефон"
              />
              <Input
                {...register('email')}
                error={!!errors.email}
                helperText={errors?.email?.message}
                type='email'
                control={control}
                label="Почта "
              />

            </SimpleGrid>
            <Button isLoading={isSubmitting} type="submit" colorScheme="green"
              variant="solid">
        {initialValues ? 'Сохранить изменения' : 'Добавить менеджера'}
      </Button>
          </TabPanel>
          <TabPanel>

          <Stack w={{base:'full', md: '359px'}} spacing='1.5rem' >
            <Text fontSize='16px' fontWeight='700'>Изменить пароль</Text>
             <Input
                {...register('oldpassword')}
                error={!!errors.oldpassword}
                helperText={errors?.oldpassword?.message}
                type="password"
                control={control}
                label="Старый пароль"
              />
             <Input
                {...register('newlogin')}
                error={!!errors.newlogin}
                helperText={errors?.newlogin?.message}
                type="password"
                control={control}
                label="Новый пароль"
              />
              <Button colorScheme='green'  variant="solid" w={{base:'full' ,md:'246px'}}>Изменить пароль</Button>

          </Stack>
          <Box mt='2rem' mb='2rem'>
            <Divider/>
          </Box>
          <Stack w={{base:'full', md: '359px'}} spacing='1.5rem' >
            <Text fontSize='16px' fontWeight='700'>Изменить логин</Text>
             <Input
                {...register('oldlogin')}
                error={!!errors.oldlogin}
                helperText={errors?.oldlogin?.message}
                type="text"
                control={control}
                label="Старый логин"
              />
             <Input
                {...register('newlogin')}
                error={!!errors.newlogin}
                helperText={errors?.newlogin?.message}
                type="text"
                control={control}
                label="Новый логин"
              />
              <Button colorScheme='blue'  variant="solid" w={{base:'full' ,md:'246px'}}>Изменить логин</Button>

          </Stack>
          </TabPanel>
        </TabPanels>
      </Tabs>

    </Form>
  )
}

