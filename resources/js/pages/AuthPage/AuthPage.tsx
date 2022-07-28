import React from 'react'

import {Button, IconButton, InputGroup, InputRightElement, useBoolean} from '@chakra-ui/react'
import {useForm} from 'react-hook-form'
import {yupResolver} from '@hookform/resolvers/yup'
import * as Yup from 'yup'
import {ViewIcon, ViewOffIcon} from '@chakra-ui/icons'

import {Icons} from '../../components/UI/iconComponents'
import classes from './AuthPage.module.css'
import {Input} from '../../components/UI/Input'
import {Form} from '../../components/UI/Form'
import {errors} from '../../errors'
import {authApi} from '../../api'
import {IAuthValues} from '../../api/auth'
import {useHistory} from 'react-router-dom'

const validationSchema = Yup.object().shape({
  email: Yup.string().required(errors.required).email(errors.email),
  password: Yup.string().required(errors.required),
})

export const AuthPage: React.FC = () => {
  const [show, setShow] = useBoolean()
  const handleClick = () => setShow.toggle()
  const history = useHistory()

  const {
    register,
    handleSubmit,
    formState: {errors, isSubmitting},
  } = useForm<IAuthValues>({
    resolver: yupResolver(validationSchema),
  })

  const submitHandler = async (data: IAuthValues) => {
    await authApi.login(data).then((res) => {

      history.push('/')})
  }

  return (
    <div className={classes.Auth}>
      <div className={classes.Logo}>
        <Icons.IconLogo w={40} h={12} />
      </div>
      <div className={classes.Body}>
        <div>
          <Form onSubmit={handleSubmit(submitHandler)}>
            <h1>Вход</h1>
            <Input {...register('email')} error={!!errors.email} helperText={errors?.email?.message} label="Логин" />
            <InputGroup mt={6} mb={8}>
              <Input
                {...register('password')}
                error={!!errors.password}
                helperText={errors?.password?.message}
                type={show ? 'text' : 'password'}
                label="Пароль"
              />
              <InputRightElement h="100%">
                <IconButton
                  mt="auto"
                  size="sm"
                  mb={2}
                  variant="ghost"
                  onClick={handleClick}
                  icon={show ? <ViewOffIcon /> : <ViewIcon />}
                  aria-label={show ? 'Скрыть' : 'Показать'}
                />
              </InputRightElement>
            </InputGroup>
            <Button isLoading={isSubmitting} type="submit" colorScheme="blue" w="100%">
              Войти
            </Button>
          </Form>
        </div>
      </div>
      <p className={classes.Title}>2005 - {new Date().getFullYear()} ООО ЧОП «АКМ-групп»</p>
    </div>
  )
}
