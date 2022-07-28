import React, {forwardRef, useImperativeHandle} from 'react'
import {Controller, useForm} from 'react-hook-form'
import * as Yup from 'yup'
import {yupResolver} from '@hookform/resolvers/yup'
import moment from 'moment'
import {Form} from '../UI/Form'
import {Input} from '../UI/Input'
import classes from './ReportsForm.module.css'
import {errors} from '../../errors'
import {Button, Select, FormControl, FormLabel} from '@chakra-ui/react'
import {CustomSelect} from '@components/UI/FormSelect/CustomSelect'
export interface IReportsFormValues {
  from: string
  to: string
  type: string | number
}

interface IReportsFormProps {
  initialValues?: IReportsFormValues
  selectHandler?: any
  changeHandler?: any
  isFetching: boolean
  ref: any

  submitHandler(data: IReportsFormValues): void
}

const validationSchema = Yup.object().shape({
  from: Yup.date().required(errors.required),
  to: Yup.date().required(errors.required),
})

const options = [
  {
    label: 'По работникам',
    value: 1,
  },
  {
    label: 'По объектам',
    value: 2,
  },
  {
    label: 'По менеджерам',
    value: 3,
  },
]

const defaultValues: IReportsFormValues = {
  from: moment().format('YYYY-MM-DD'),
  to: moment().format('YYYY-MM-DD'),
  type: 1,
}

export const ReportsForm: React.FC<IReportsFormProps> = forwardRef((props, ref) => {
  const {initialValues, submitHandler, isFetching, selectHandler} = props
  const {
    register,
    handleSubmit,
    control,
    formState: {errors, isSubmitting},
  } = useForm<IReportsFormValues>({
    defaultValues: initialValues || defaultValues,
    resolver: yupResolver(validationSchema),
  })

  useImperativeHandle(ref, () => ({
    handleSubmit,
  }))

  return (
    <>
      <Form className={classes.wrapper} onSubmit={handleSubmit(submitHandler)}>
        <div>
          <Controller
            name="type"
            control={control}
            render={({field}) => {
              return (
                <>
                  <FormControl>
                    <FormLabel htmlFor="Отчет по">Отчет по</FormLabel>
                    <Select
                      size="lg"
                      borderRadius="none"
                      label="Отчет по"
                      fontSize="14px"
                      onChange={selectHandler}
                      {...field}
                    >
                      {options?.map((option) => (
                        <option
                          style={{display: 'flex', alignItems: 'center', justifyContent: 'center'}}
                          key={option.value}
                          value={option.value}
                        >
                          {option.label}
                        </option>
                      ))}
                    </Select>
                  </FormControl>
                </>
              )
            }}
          />
          {/* <Controller
              name="type"
              control={control}
              render={({ field }) => {
                console.log(field, 'field')
                return <CustomSelect onChange={selectHandler}  {...field}
                                     options={options} label="Отчет по"/>
              }}
            /> */}

          <Input
            {...register('from')}
            label="Период, с"
            type="date"
            error={!!errors.from}
            helperText={errors?.from?.message}
          />
          <Input
            {...register('to')}
            label="Период, до"
            type="date"
            error={!!errors.to}
            helperText={errors?.to?.message}
          />
          <Button isLoading={isFetching} type="submit" colorScheme="blue" variant="solid">
            Сформировать отчет
          </Button>
        </div>
      </Form>
    </>
  )
})
