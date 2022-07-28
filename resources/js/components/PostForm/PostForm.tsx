import React from 'react'

import { Controller, useFieldArray, useForm } from 'react-hook-form'
import { yupResolver } from '@hookform/resolvers/yup'
import * as Yup from 'yup'
import { Tab, TabList, TabPanel, TabPanels, Tabs } from '@chakra-ui/tabs'
import moment from 'moment'
import {
  Box,
  Button,
  CloseButton,
  Divider,
  Heading,
  HStack,
  NumberInput,
  NumberInputField,
} from '@chakra-ui/react'

import { Form } from '@components/UI/Form'
import { Input } from '@components/UI/Input'
import { errors } from '@app/errors'
import {
  days,
  DEFAULT_INTERVAL,
  format,
  IInterval,
  IntervalInput,
} from '@components/IntervalInput/IntervalInput'
import { IPostFormValues, IUnixInterval } from '@models/post'
import { Icons } from '@components/UI/iconComponents'


interface IPostFormProps {
  initialValues?: Partial<IPostFormValues<IInterval>>

  submitHandler(data: IPostFormValues<IInterval>): Promise<void>
}


const defaultValues = {
  name: '',
  phone: '',
  Mon: { salary: null, times: [] },
  Tue: { salary: null, times: [] },
  Wed: { salary: null, times: [] },
  Thu: { salary: null, times: [] },
  Fri: { salary: null, times: [] },
  Sat: { salary: null, times: [] },
  Sun: { salary: null, times: [] },
}

const MAX_NOT_STANDARD_WORKS = 10

export const mergeDatesToUnix = (
  times: IInterval[], interval = DEFAULT_INTERVAL): IUnixInterval[] => {
  const delta = interval * 60
  const result: { from: number; to: number }[] = []
  let first = 0
  if (times.length) {
    times.sort((a, b) => moment(a.date).unix() - moment(b.date).unix()).reduce(
      (prev, cur, index) => {
        if (first === 0) {
          first = moment(index === 0 ? cur.date : prev.date).unix()
        }
        if ((prev.date && moment(cur.date).unix() - moment(prev.date).unix() >
          delta) || index + 1 === times.length) {
          result.push({
            from: first,
            to:
              index + 1 === times.length && moment(cur.date).unix() -
              moment(prev.date).unix() === delta
                ? moment(prev.date).unix() + delta
                : moment(prev.date).unix(),
          })
          first = 0
        }
        return cur
      },
      { date: '', time: '' },
    )
  }
  return result
}

export const unixToDatesIntervals = (
  times: IUnixInterval[], interval = DEFAULT_INTERVAL): IInterval[] => {
  const result: IInterval[] = []
  times.forEach(({ from, to }) => {
    const start = new Date(from * 1000)
    const end = new Date(to * 1000)
    for (let d = start; d <= end; d.setMinutes(d.getMinutes() + interval)) {
      result.push(format(d))
    }
  })
  return result
}

const validationSchema = Yup.object().shape({
  name: Yup.string().required(errors.required),
  phone: Yup.string().
    required(errors.required).
    matches(/^(?:[+\d].*\d|\d)$/, errors.phoneLength).
    length(16, errors.phoneLength),
  Mon: Yup.object().shape({
    // salary: Yup.number()
  }),
  Tue: Yup.object().shape({
    // salary: Yup.number().min(0, errors.minNumber),
  }),
  Wed: Yup.object().shape({
    // salary: Yup.number().min(0, errors.minNumber),
  }),
  Thu: Yup.object().shape({
    // salary: Yup.number().min(0, errors.minNumber),
  }),
  Fri: Yup.object().shape({
    // salary: Yup.number().min(0, errors.minNumber),
  }),
  Sat: Yup.object().shape({
    // salary: Yup.number().min(0, errors.minNumber),
  }),
  Sun: Yup.object().shape({
    // salary: Yup.number().min(0, errors.minNumber),
  }),
})

export const PostForm: React.FC<IPostFormProps> = (props) => {
  const { initialValues, submitHandler } = props

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
    control,
  } = useForm<IPostFormValues<IInterval>>({
    defaultValues: initialValues || defaultValues,
    resolver: yupResolver(validationSchema),
  })

  const { fields, append, remove } = useFieldArray(
    { control, name: 'nonStandardWork' })

  const handleAdd = () => {
    append({
      salary: 0,
      day: moment().add(fields.length, 'd').format('YYYY-MM-DD'),
      times: [],
    })
  }

  const handleDelete = (id: number) => {
    remove(id)
  }

  return (
    <Form onSubmit={handleSubmit(submitHandler)}>
      <Tabs colorScheme="blue">
        <TabList mb={6}>
          <Tab>Основные данные</Tab>
          <Tab>Режим работы</Tab>
        </TabList>

        <TabPanels>
          <TabPanel padding={0} mb={6}>
            <HStack spacing={7}>
              <Input
                {...register('name')}
                error={!!errors.name}
                helperText={errors?.name?.message}
                label="Название поста"
              />
              <Input
                {...register('phone')}
                error={!!errors.phone}
                helperText={errors?.phone?.message}
                type="tel"
                control={control}
                label="Номер телефона поста"
              />
            </HStack>
          </TabPanel>

          <TabPanel padding={0} mb={6}>
            {Object.keys(days).map((day: keyof typeof days) => {
              return (
                <Controller
                  key={day}
                  name={day}
                  control={control}
                  render={({ field: { onChange, name, value } }) => {
                    return (
                      <IntervalInput value={value} day={name}
                                     onChange={onChange}>
                        <NumberInput min={0} max={999}
                                     width={{ base: '100px', md: '142px' }}
                                     marginRight={{ base: '4rem', md: 'full' }}>
                          <NumberInputField
                            fontSize="14px"
                            {...register(`${name}.salary`)}
                            label="Ставка ₽/час"
                            placeholder="Ставка ₽/час"
                            type="number"
                            min={0}
                            marginBottom={{ base: '1.5rem', md: 'full' }}
                          />
                        </NumberInput>
                      </IntervalInput>
                    )
                  }}
                />
              )
            })}
            <Divider/>
            <Heading as="h5" size="md" mb={4}>
              Нестандартный режим
            </Heading>
            {fields.map((nonStandardWork, index) => {
              return (
                <Controller
                  key={nonStandardWork.id}
                  name={`nonStandardWork.${index}`}
                  control={control}
                  render={({ field: { onChange, name, value } }) => {
                    return (
                      <IntervalInput value={value} onChange={onChange}
                                     isNotStandard>
                        <HStack spacing="2rem">
                          <Box>
                            <Input
                              {...register(`${name}.day`)}
                              error={!!errors[name]?.day}
                              helperText={errors[name]?.day?.message}
                              label="Дата"
                              type="date"
                            />

                          </Box>
                          <Box marginTop={{ base: '1rem', md: 'full' }}>
                            <Input
                              {...register(`${name}.salary`)}
                              error={!!errors[name]?.salary}
                              helperText={errors[name]?.salary?.message}
                              label="Ставка ₽/час"
                              type="number"
                            />

                          </Box>
                          <CloseButton type="button" m="auto" size="sm"
                                       onClick={() => handleDelete(index)}/>
                        </HStack>
                      </IntervalInput>
                    )
                  }}
                />
              )
            })}
            <Button
              onClick={handleAdd}
              type="button"
              px={0}
              disabled={fields.length >= MAX_NOT_STANDARD_WORKS}
              leftIcon={<Icons.IconPlus/>}
              colorScheme="grey"
              variant="ghost"
              mb={3}
            >
              Добавить нестандартный режим работы
            </Button>
          </TabPanel>
        </TabPanels>
      </Tabs>
      <Button isLoading={isSubmitting} type="submit" colorScheme="green"
              variant="solid">
        {initialValues ? 'Сохранить изменения' : 'Добавить пост'}
      </Button>
    </Form>
  )
}
