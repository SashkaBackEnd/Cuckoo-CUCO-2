import { managerAPI } from '@app/services'
import { USER_ID_KEY } from '@app/api'
import { IManager } from '@models/manager'


/**
 * @Returns logged-in user,
 * this is an asynchronous task
 */


export const useCurrentUser = (): { manager: IManager, isLoading: boolean, isError: boolean } => {
  const { data: manager, isLoading, isError } = managerAPI.useFetchManagerByIdQuery(
    localStorage.getItem(USER_ID_KEY))
  return { manager, isLoading, isError }
}
