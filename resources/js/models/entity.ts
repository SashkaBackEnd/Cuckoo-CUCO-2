import {IPost} from './post'

export interface IEvents {
  name?: string
  created_at: string
  guarded_object_id: number
  id: number
  security_guard_id: number
  type: string
  value_1: string | null
}

export interface IShiftManagers {
  id?: string
  entity: IEntity
  guarded_objects_count: number
  worker_count: number
}

export interface IEntity {
  shortName: IEntity
  events?: IEvents[]
  address: string
  callBackQuantity: number
  callFrom: number
  callTo: number
  centralPost: IPost
  centralPostId: string
  comment: string
  createdAt: string
  customerContacts: null
  customerName: string
  customers: ICustomer[]
  dialingStatus: 1 | 0
  posts: IPost[]
  id: string
  maxDurationWork: number
  name: string
  phone: string
  quantityCalls: number
  servicePhone: string
  managers_count: number
  workers_count: number
  last_check: {
    type: string
    date: number
  }
}

export interface ICustomer {
  id: number
  name: string
  contact: string
}

export interface IEntityFormValues {
  id: string
  originalId: string
  centralPostId: string
  name: string
  address: string
  phone: string
  servicePhone: string
  comment: string
  customerName: string
  callFrom: string | number
  callTo: string | number
  quantityCalls: number | string
  callBackQuantity: number | string
  maxDurationWork: string | number
  dialingStatus: 1 | 0
  customers: ICustomer[]
}
