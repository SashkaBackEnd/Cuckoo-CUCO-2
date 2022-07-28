import axios, { AxiosResponse } from 'axios'
import { toast } from '../theme'
import { customReload } from '@app/helpers/useReload'


export interface IAuthValues {
  email: string
  password: string
}


interface IAuthResponse {
  apiToken: string
  userId: string
}


export const TOKEN_KEY = 'cuckoo_token'
export const USER_ID_KEY = 'cuckoo_userId'
// const history = useHistory()


export const authApi = {
  login: async (data: IAuthValues): Promise<void> => {
    await axios.post('/api/login', data).
      then(({ data }: AxiosResponse<IAuthResponse>) => {
        localStorage.setItem(TOKEN_KEY, data.apiToken)
        localStorage.setItem(USER_ID_KEY, data.userId)
        toast({ title: 'Авторизация', description: 'Прошла успешно' })
      }).
      catch((error) => {
        toast({
          status: 'error',
          title: error.response.data.error,
          description: 'Проверьте поля ввода',
        })
      })
  },
  logout: (): void => {
    localStorage.removeItem(TOKEN_KEY)
    localStorage.removeItem(USER_ID_KEY)
    localStorage.clear()
    window.location.reload()
  },
}


