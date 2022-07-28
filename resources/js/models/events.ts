import { IManager } from '@models/manager'
import { IEntity } from '@models/entity'


export interface ISecurityGuard {
  active: 1 | 0
  id: string
  name: string
  phone: string
  pin: string
  shortName: string
  salary: {
    from: number
    salary: number
  }
}
export interface IFastEvent {
  countDialingErrors: number
  countErrorsFinishedJob: number
  countErrorsStartingJob: number
  countPostsWithoutGuards: number
}

/* eslint-disable */
export enum events {
  countPostsWithoutGuards = 'Постов без работников',
  countDialingErrors = 'Ошибок при дозвоне',
  countErrorsStartingJob = 'Ошибок при заступлении на смену',
  countErrorsFinishedJob = 'Ошибок при завершении смены'
}

interface IGuardedObject {
  id: string
  name: string
  phone: string
}

export interface IFilteredQueryBody {
  date_from?: number,
  date_to?: number,
  entity_id?: string,
  status?: string,
  manager_id?: number,
  security_guard_id?: number,
  guarded_object_id?: number
}

export interface IEvents {
  id:string
  name?:string
  date: string
  type?: string
  securityGuard?:ISecurityGuard
  value?: string
  salary?: string,
  manager: IManager[],
  entity: IEntity,
  guardedObject: IGuardedObject
}
