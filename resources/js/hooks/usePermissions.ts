import { useCurrentUser } from '@hooks/useCurrentManager'
import {
  IAccessObj,
  PermissionTypes,
} from '@components/AccessSettings/ManagerInAccess'


interface IUsePermissionsValue {
  isEdit: boolean,
  isView: boolean
}


/**
 *
 * @param section is the system section to check if current manager has acces or not.
 */
export const usePermissions = (section: string): IUsePermissionsValue => {
  const {manager: currentUser } = useCurrentUser()
  const access: IAccessObj = JSON.parse(currentUser?.access)
  const isEdit = access && (access[section] === PermissionTypes.edit)
  const isView = access && (access[section] === PermissionTypes.view)

  return {
    isEdit: isEdit || currentUser?.roleType === 1,
    isView: isView || currentUser?.roleType === 1,
  }
}
