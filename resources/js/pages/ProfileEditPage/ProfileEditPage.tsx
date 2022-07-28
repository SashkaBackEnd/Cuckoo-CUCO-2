import React from 'react'

import {Tab, TabList, TabPanel, TabPanels, Tabs} from '@chakra-ui/tabs'
import {Divider, Heading} from '@chakra-ui/layout'

import {BackToMain} from '@components/BackToMain'
import {ProfileForm} from '@components/ProfileForm'
import {LoginForm} from '@components/LoginForm'
import {PasswordForm} from '@components/PasswordForm'
import {IProfileFormValues} from '@components/ProfileForm/ProfileForm'
import {ILoginFormValues} from '@components/LoginForm/LoginForm'
import {IPasswordFormValues} from '@components/PasswordForm/PasswordForm'
import { Page, PageBody, toast } from '@app/theme'
import { useCurrentUser } from '@hooks/useCurrentManager'
import { managerAPI } from '@app/services'
import { useHistory } from 'react-router-dom'
import { errorHandler } from '@app/errors'

export const ProfileEditPage: React.FC = () => {
  const {manager} = useCurrentUser()
  const [updateUser, {isLoading}] = managerAPI.useUpdateManagerMutation()
  const history = useHistory()

  const submitHandler = async (data: IProfileFormValues) => {
    const normalizedData = {
      ...data,
      email: manager.email,
    }

    await updateUser({ manager, data: normalizedData }).then(() => {
      toast({
        title: "Данные обновлены",
        status: "success"
      })
      history.push("/entities")
    }).catch((err) => {
      errorHandler(err)
    })
  }
  const loginSubmitHandler = async (data: ILoginFormValues) => {
    const normalizedData = {
      ...data,
      name: manager.name,
      email: manager.email
    }


    await updateUser({manager, data: normalizedData })
  }
  const passwordSubmitHandler = async (data: IPasswordFormValues) => {
    await setTimeout(() => console.log(data), 1)
  }
  return (
    <div>
      <BackToMain />
      <Page>
        <PageBody bg="white" p={10}>
          <Heading as="h2" size="lg" mb={8}>
            Мой профиль
          </Heading>
          <Tabs colorScheme="blue">
            <TabList mb={6}>
              <Tab>Личные данные</Tab>
              <Tab>Настройки учетной записи</Tab>
            </TabList>

            <TabPanels>
              <TabPanel padding={0}>
                <ProfileForm initialValues={manager}  submitHandler={submitHandler} />
              </TabPanel>

              <TabPanel padding={0}>
                <LoginForm submitHandler={loginSubmitHandler} />
                <Divider />
                <PasswordForm submitHandler={passwordSubmitHandler} />
              </TabPanel>
            </TabPanels>
          </Tabs>
        </PageBody>
      </Page>
    </div>
  )
}
