/* eslint-disable */

import { authApi } from 'api'
import { toast } from 'theme'


interface IPayload {
  data: {
    message: string
  },
  status: number
}

interface IErrorType {
  data: string
  error: string
  originalStatus: number
  status: string
  payload: IPayload
}


export const enum errors {
  required = 'Поле обязательно для заполнения',
  email = 'Укажите корректный email',
  idLength = 'Необходимая длина 4 символа',
  numbers = 'Только цифры',
  phoneLength = 'Неверно указан телефон',
  minNumber = 'Минимальное значение - 0',
  todayDate = 'Минимальная дата - завтра',
  adult = 'Минимальный возраст - 18 лет',
}


/**
 * Toast error handler
 * @param error
 */
export const errorHandler: (error: IErrorType) => void = (error) => {

  if (error?.data?.includes('html')) {
    return
  }


  switch (error?.payload?.status) {
    // case 401:
    //   authApi.logout()
    //   window.location.reload()
    //   toast({
    //     status: 'info',
    //     title: 'Another device logged in',
    //   })
    //   break
    case 400:
    case 402:
    case 403:
      toast({
        status: 'error',
        title: error.payload.data.message,
      })
      break
    case 404:
      // toast({
      //   status: 'error',
      //   title: error.payload.data.message || 'Упс, такая страница не существует',
      // })
      break
    case 409:
      toast({
        status: 'error',
        title: 'Этот менеджер уже прикреплен',
      })
      break
    case 410:
      window.location.reload()
      break
    case 422:
      Object.values(error).map((values: string[]) => {
        toast({
          status: 'error',
          title: values.join(', ') + 'hello',
        })
      })
      break
    case 423:
      toast({
        status: 'error',
        title: error.payload.data.message,
      })
      window.location.reload()
      break
    default:
      // toast({
      //   status: 'error',
      //   title: error.payload.data?.message || "Ошибка сервера",
      // })
  }
}

//
// export const errorHandler: (error: AxiosError ) => void = (error) => {
//   switch (error.response?.status) {
//     case 401:
//       authApi.logout()
//       window.location.reload()
//       break
//     case 400:
//     case 402:
//     case 403:
//       toast({
//         status: 'error',
//         title: error.response.data.message,
//       })
//       break
//     case 404:
//       toast({
//         status: 'error',
//         title: error.response?.data?.message || 'Упс, такая страница не существует',
//       })
//       break
//     case 410:
//       window.location.reload()
//       break
//     case 422:
//       Object.values(error.response.data.errors).map((values: string[]) => {
//         toast({
//           status: 'error',
//           title: values.join(', '),
//         })
//       })
//       break
//     case 423:
//       toast({
//         status: 'error',
//         title: error.response.data.message,
//       })
//       window.location.reload()
//       break
//     default:
//       toast({
//         status: 'error',
//         title: error.message,
//       })
//   }
// }
