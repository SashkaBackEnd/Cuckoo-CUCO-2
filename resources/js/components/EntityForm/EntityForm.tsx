import React from 'react'

import {useFieldArray, useForm, Controller} from 'react-hook-form'
import {yupResolver} from '@hookform/resolvers/yup'
import * as Yup from 'yup'
import {Tab, TabList, TabPanel, TabPanels, Tabs} from '@chakra-ui/tabs'
import {Button, CloseButton, HStack, Stack, Wrap} from '@chakra-ui/react'

import {Form} from '../UI/Form'
import {Input} from '../UI/Input'
import FormCheckbox from '../UI/FormCheckbox/FormCheckbox'
import {errors} from '@app/errors'
import classes from './EntityForm.module.css'
import {Icons} from '../UI/iconComponents'
import {FormSelect} from '../UI/FormSelect'
import {IOption} from '../UI/FormSelect/FormSelect'
import {IEntityFormValues} from '@models/entity'

interface IEntityCreateProps {
  initialValues?: IEntityFormValues
  postOptions?: IOption[]

  submitHandler(data: IEntityFormValues): Promise<void>
}

const validationSchema = Yup.object().shape({
  name: Yup.string().required(errors.required),
  customerName: Yup.string().required(errors.required),
  phone: Yup.string()
    .required(errors.required)
    .matches(/^(?:[+\d].*\d|\d)$/, errors.phoneLength)
    .length(16, errors.phoneLength),
  address: Yup.string().required(errors.required),
  // quantityCalls: Yup.number().required(errors.required).min(0, errors.minNumber),
  // callBackQuantity: Yup.number().required(errors.required).min(0, errors.minNumber),
  // callFrom: Yup.number().required(errors.required).min(0, errors.minNumber),
  // maxDurationWork: Yup.number().required(errors.required).min(0, errors.minNumber),
  // dialingStatus: Yup.string().required(errors.required),
  id: Yup.string().required(errors.required).matches(/^\d+$/, errors.numbers).length(4, errors.idLength),
  servicePhone: Yup.string()
    .required(errors.required)
    .matches(/^(?:[+\d].*\d|\d)$/, errors.phoneLength)
    .length(16, errors.phoneLength),
})

const defaultValues: IEntityFormValues = {
  id: '',
  originalId: '',
  centralPostId: '',
  name: '',
  address: '',
  phone: '',
  servicePhone: '',
  comment: '',
  customerName: '',
  callFrom: '',
  callTo: '',
  quantityCalls: '0',
  callBackQuantity: '0',
  maxDurationWork: '0',
  dialingStatus: 0,
  customers: [],
}

const MAX_CUSTOMERS = 5

export const EntityForm: React.FC<IEntityCreateProps> = (props) => {
  const {initialValues, submitHandler, postOptions} = props

  const {
    register,
    handleSubmit,
    formState: {errors, isSubmitting},
    control,
  } = useForm<IEntityFormValues>({
    defaultValues: initialValues || defaultValues,
    resolver: yupResolver(validationSchema),
  })

  const {fields, append, remove} = useFieldArray({control, name: 'customers'})

  const handleAdd = () => {
    append({id: Date.now(), contact: '', name: ''})
  }

  const handleDelete = (index: number) => {
    remove(index)
  }

  return (
    <Form onSubmit={handleSubmit(submitHandler)}>
      <Tabs colorScheme="blue">
        <TabList mb={6}>
          <Tab>
            <b>???????????????? ????????????</b>
          </Tab>
          <Tab>
            <b>?????????????????? ??????????????</b>
          </Tab>
        </TabList>

        <TabPanels>
          <TabPanel padding={0}>
            <p className={classes.Title}>???????????? ??????????????</p>
            <HStack spacing={7} mb={6}>
              <Input
                {...register('id')}
                error={!!errors.id}
                helperText={errors?.id?.message}
                maxLength={4}
                label="ID ??????????????"
              />
              {postOptions ? (
                <Controller
                  name="centralPostId"
                  control={control}
                  render={({field}) => {
                    return <FormSelect {...field} label="ID ???????????????????????? ??????????" options={postOptions} isClearable />
                  }}
                />
              ) : null}
            </HStack>
            <HStack spacing={7} mb={6}>
              <Input
                {...register('name')}
                error={!!errors.name}
                helperText={errors?.name?.message}
                label="???????????????? ??????????????"
              />
              <Input
                {...register('address')}
                error={!!errors.address}
                helperText={errors?.address?.message}
                label="??????????"
              />
            </HStack>
            <HStack spacing={7} mb={6}>
              <Input
                {...register('phone')}
                error={!!errors.phone}
                helperText={errors?.phone?.message}
                type="tel"
                control={control}
                label="??????????????"
              />
              <Input
                {...register('servicePhone')}
                error={!!errors.servicePhone}
                helperText={errors?.servicePhone?.message}
                type="tel"
                control={control}
                label="?????????????????? ??????????????"
              />
            </HStack>
            <HStack spacing={7} mb={6}>
              <Input {...register('comment')} textarea={true} label="??????????????????????" />
            </HStack>
            <p className={classes.Title}>???????????? ??????????????????</p>
            <HStack spacing={7} mb={6}>
              <Input
                {...register('customerName')}
                error={!!errors.customerName}
                helperText={errors?.customerName?.message}
                label="???????????????? ??????????????????"
              />
            </HStack>
            {fields.map((customer, index) => {
              return (
                <HStack spacing={7} mb={6} alignItems="flex-end" key={customer.id}>
                  <Input {...register(`customers.${index}.contact`)} label={`???????????????? ?????????????????? ???${index + 1}`} />
                  <Input {...register(`customers.${index}.name`)} label={`?????? ???${index + 1}`} />
                  <CloseButton type="button" m="auto" size="lg" onClick={() => handleDelete(index)} />
                </HStack>
              )
            })}
            <div>
              <Button
                onClick={handleAdd}
                type="button"
                px={0}
                disabled={fields.length >= MAX_CUSTOMERS}
                leftIcon={<Icons.IconPlus />}
                colorScheme="grey"
                variant="ghost"
                mb={3}
              >
                ???????????????? ??????????????
              </Button>
            </div>
          </TabPanel>

          <TabPanel padding={0}>
            <Stack direction={{base: 'column', md: 'row'}} spacing={7} mb={6} alignItems={'flex-end'}>
              <Input {...register('callFrom')} label="????????????, ??:" type="time" />
              <Input {...register('callTo')} label="????????????, ????:" type="time" />
              <Input {...register('quantityCalls')} type="number" label="??????-???? ??????????????" />
              <Input {...register('callBackQuantity')} type="number" label="??????-???? ????????????????????" />
              <Input
                {...register('maxDurationWork')}
                type="number"
                label="???????????????????????? ?????????? ?????????? "
                placeholder={'????????????????, 12'}
              />
            </Stack>

            <HStack spacing={7} mb={6}>
              <Controller
                name="dialingStatus"
                control={control}
                render={({field: {onChange, value, name, ref}}) => {
                  return (
                    <FormCheckbox name={name} label="???????????? ??????????????" onChange={onChange} value={value} inputRef={ref} />
                  )
                }}
              />
            </HStack>
          </TabPanel>
        </TabPanels>
      </Tabs>

      <Button
        onClick={handleSubmit(submitHandler)}
        isLoading={isSubmitting}
        type="submit"
        colorScheme="green"
        variant="solid"
      >
        {initialValues ? '?????????????????? ??????????????????' : '???????????????? ????????????'}
      </Button>
    </Form>
  )
}
