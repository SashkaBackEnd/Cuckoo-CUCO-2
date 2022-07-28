import { IEntity, IShiftManagers } from '@models/entity'
import { IAccessObj } from '@components/AccessSettings/ManagerInAccess'
import { ILog } from '@models/post'


export interface IAttachManagerBody {
  entity_id: string
  user_id: string
}


export interface IDeactivateManagerBody {
  user_id: string,
  message: string
}


export interface IManager {

  id: string
  pin: string
  name: string
  surname: string
  patronymic: string
  phone: string
  entities: IShiftManagers[],
  oldpassword: string,
  newpassword: string,
  oldlogin: string,
  newlogin?: string
  access: string
  roleType?: number
  events?: ILog[]
  log?: ILog[]
  guarded_objects_count: string
  worker_count: string
  status: number;
  email: string
}
export interface IManagerShift{
  id?: string
  address?: string
  name?: string
  phone?: string
  service_phone?: string
  quantity_calls?:string
  pivot?: IAttachManagerBody[]
  comment?:string


}

export interface IManagerFormValues {
  id?: string
  pin?: string
  email:string
  name: string
  phone: string
  patronymic: string
  surname: string
}
