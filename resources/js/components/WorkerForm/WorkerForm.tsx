import React from 'react'

import {Controller, useForm} from 'react-hook-form'
import {yupResolver} from '@hookform/resolvers/yup'
import {Tab, TabList, TabPanel, TabPanels, Tabs} from '@chakra-ui/tabs'
import {Button, SimpleGrid} from '@chakra-ui/react'
import * as Yup from 'yup'
import moment from 'moment'

import {Form} from '../UI/Form'
import {Input} from '../UI/Input'
import {errors} from '@app/errors'
import {FormSelect} from '../UI/FormSelect'
import {TWorkerLicenseRank, TWorkerStatus} from '@models/worker'

interface IWorkerFormProps {
  initialValues?: IWorkerFormValues

  submitHandler(data: IWorkerFormValues): Promise<void>
}

export interface IWorkerFormValues {
  name: string
  surname: string
  patronymic: string
  birthDate: string
  phone: string
  status: string
  license: 1 | 0
  licenseRank: TWorkerLicenseRank
  licenseToDate: string
  drivingLicense: 1 | 0
  car: string
  workType: string | number
  medicalBook: string
  gun: string
  leftThings: string
  depts: string
  comment: string
  knewAboutUs: string
}

export interface IManagerFormValues {
  name: string
  surname: string
  patronymic: string
  birthDate: string
  phone: string
  status: string
  license: 1 | 0
  licenseRank: TWorkerLicenseRank
  licenseToDate: string
  drivingLicense: 1 | 0
  car: string
  workType: string | number
  medicalBook: string
  gun: string
  leftThings: string
  depts: string
  comment: string
  knewAboutUs: string
}

const validationSchema = Yup.object().shape({
  name: Yup.string().required(errors.required),
  surname: Yup.string().required(errors.required),
  patronymic: Yup.string().required(errors.required),
  status: Yup.string().required(errors.required),
  phone: Yup.string()
    .required(errors.required)
    .matches(/^(?:[+\d].*\d|\d)$/, errors.phoneLength)
    .length(16, errors.phoneLength),
  birthDate: Yup.date()
    .required(errors.required)
    .max(moment().subtract(18, 'years').format('YYYY-MM-DD'), errors.adult),
  licenseToDate: Yup.date()
    .notRequired()
    .when('license', {
      is: (value) => value,
      then: (rule) => rule.min(new Date(), errors.todayDate),
    }),
})
const statusOptions = [
  {label: 'Обычный', value: 'обычный'},
  {label: 'Служебный', value: 'служебный'},
]
const booleanOptions = [
  {label: 'Нет', value: 0},
  {label: 'Есть', value: 1},
]
const workTypeOptions = [
  {label: 'Смены', value: 'смены'},
  {label: 'Вахта', value: 'вахта'},
]
const licenseRankOptions = [
  {label: '1 разряд', value: 1},
  {label: '2 разряд', value: 2},
  {label: '3 разряд', value: 3},
  {label: '4 разряд', value: 4},
  {label: '5 разряд', value: 5},
  {label: '6 разряд', value: 6},
  {label: '7 разряд', value: 7},
  {label: '8 разряд', value: 8},
  {label: '9 разряд', value: 9},
]
const defaultValues: IWorkerFormValues = {
  name: '',
  surname: '',
  patronymic: '',
  // birthDate: moment().subtract(18, 'years').format('YYYY-MM-DD'),
  birthDate: moment().format(''),
  phone: '',
  status: 'обычный',
  license: 0,
  licenseRank: 1,
  licenseToDate: moment().format('YYYY-MM-DD'),
  drivingLicense: 0,
  car: '',
  workType: '',
  medicalBook: '',
  gun: '',
  leftThings: '',
  depts: '',
  comment: '',
  knewAboutUs: '',
}

export const WorkerForm: React.FC<IWorkerFormProps> = (props) => {
  const {initialValues, submitHandler} = props
  const {
    register,
    handleSubmit,
    formState: {errors, isSubmitting},
    watch,
    control,
  } = useForm<IWorkerFormValues>({
    defaultValues: initialValues || defaultValues,
    resolver: yupResolver(validationSchema),
  })

  const watchShowDriverLicense = watch('drivingLicense')
  const watchShowLicense = watch('license')

  return (
    <Form onSubmit={handleSubmit(submitHandler)}>
      <Tabs colorScheme="blue">
        <TabList mb={6}>
          <Tab>Личные данные</Tab>
        </TabList>

        <TabPanels>
          <TabPanel padding={0}>
            <SimpleGrid columns={[1, 3]} spacingX={7} spacingY={6} mb={6}>
              <Input
                {...register('surname')}
                maxLength={32}
                error={!!errors.surname}
                helperText={errors?.surname?.message}
                label="Фамилия"
              />
              <Input
                {...register('name')}
                maxLength={32}
                error={!!errors.name}
                helperText={errors?.name?.message}
                label="Имя"
              />
              <Input
                {...register('patronymic')}
                maxLength={32}
                error={!!errors.patronymic}
                helperText={errors?.patronymic?.message}
                label="Отчество"
              />
              <Input
                {...register('birthDate')}
                error={!!errors.birthDate}
                helperText={errors?.birthDate?.message}
                label="Дата рождения"
                type="date"
              />
              <Input
                {...register('phone')}
                error={!!errors.phone}
                helperText={errors?.phone?.message}
                type="tel"
                control={control}
                label="Телефон"
              />
              <Controller
                name="status"
                control={control}
                render={({field}) => {
                  return <FormSelect {...field} label="Статус" options={statusOptions} />
                }}
              />
            </SimpleGrid>

            <SimpleGrid columns={[1, 3]} spacingX={7} spacingY={6} mb={6}>
              <Controller
                name="license"
                control={control}
                render={({field}) => {
                  return <FormSelect {...field} label="УЛЧО" options={booleanOptions} />
                }}
              />
              {watchShowLicense === 1 ? (
                <>
                  <Controller
                    name="licenseRank"
                    control={control}
                    render={({field}) => {
                      return <FormSelect {...field} label="Разряд УЛЧО" options={licenseRankOptions} />
                    }}
                  />
                  <Input
                    {...register('licenseToDate')}
                    error={!!errors.licenseToDate}
                    helperText={errors?.licenseToDate?.message}
                    label="Срок действия УЛЧО"
                    type="date"
                  />
                </>
              ) : null}
            </SimpleGrid>

            <SimpleGrid columns={[1, 3]} spacingX={7} spacingY={6} mb={6}>
              <Controller
                name="drivingLicense"
                control={control}
                render={({field}) => {
                  return <FormSelect {...field} label="Водительские права" options={booleanOptions} />
                }}
              />
              {watchShowDriverLicense === 1 ? (
                <Input
                  {...register('car')}
                  maxLength={255}
                  error={!!errors.car}
                  helperText={errors?.car?.message}
                  label="Личное авто"
                />
              ) : null}
            </SimpleGrid>

            <SimpleGrid columns={[1, 3]} spacingX={7} spacingY={6} mb={6}>
              <Input
                {...register('medicalBook')}
                maxLength={512}
                error={!!errors.medicalBook}
                helperText={errors?.medicalBook?.message}
                label="Медицинская книжка"
              />
              <Input
                {...register('gun')}
                maxLength={255}
                error={!!errors.gun}
                helperText={errors?.gun?.message}
                label="Личное оружие"
              />
              <Controller
                name="workType"
                control={control}
                render={({field}) => {
                  return <FormSelect {...field} label="Тип работы" options={workTypeOptions} />
                }}
              />
            </SimpleGrid>

            <SimpleGrid columns={1} spacingX={7} spacingY={6} mb={6}>
              <Input
                {...register('leftThings')}
                maxLength={1024}
                error={!!errors.leftThings}
                helperText={errors?.leftThings?.message}
                label="Оставленные вещи"
                textarea
              />
              <Input
                {...register('depts')}
                maxLength={1024}
                error={!!errors.depts}
                helperText={errors?.depts?.message}
                label="Судимости, долги, алименты"
                textarea
              />
              <Input
                {...register('comment')}
                maxLength={4000}
                error={!!errors.comment}
                helperText={errors?.comment?.message}
                label="Комментарий"
                textarea
              />
            </SimpleGrid>

            <SimpleGrid columns={[1, 3]} spacingX={7} spacingY={6} mb={6}>
              <Input
                {...register('knewAboutUs')}
                maxLength={1024}
                error={!!errors.knewAboutUs}
                helperText={errors?.knewAboutUs?.message}
                label="Откуда узнал о нас"
              />
            </SimpleGrid>
          </TabPanel>
        </TabPanels>
      </Tabs>

      <Button isLoading={isSubmitting} type="submit" colorScheme="green" variant="solid">
        {initialValues ? 'Сохранить изменения' : 'Добавить работника'}
      </Button>
    </Form>
  )
}
