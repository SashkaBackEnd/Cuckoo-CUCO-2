import { IInterval, TDays } from '@components/IntervalInput/IntervalInput'


export interface ILastListCheck {
  date: number
  type: string
}

/* eslint-disable */
export enum log {
  checkPost = 'Проверил пост',
  endShift = 'Завершил работу',
  startShift = 'Заступил на пост',
  checkPassed = 'Автоматическая проверка поста прошла успешно',
  checkFailed = 'Автоматическая проверка поста не пройдена',
  smsForHeadOfSecurity = 'Отправлено SMS-уведомление',
  customCheckPassed = 'Ручная проверка пройдена',
  customCheckFailed = 'Ручная проверка провалена',
  shortEndShiftTry = 'Пытался завершить смену без сменщика',
  timeoutEndShift = 'Смена завершена принудительно',
  sos = 'Команда SOS',
  sosEnd = 'Тревога снята вручную',// red
  autoSosEnd = 'Тревога снята автоматически',//red
  shiftCanceled = 'Аннулирована смена',
  dialPaused = 'Обзвон приостановлен', // red
  dialResumed = 'Обзвон возобновлен', // green
  objectGuardMismatch = 'Охранник с одного поста пытается начать смену на другом посту',
  guardActivate = 'Статус охранника изменен на активный',
  guardDeactivate = 'Статус охранника изменен на неактивный',
  shiftTimeExceeded = 'Превышена максимальная продолжительность смены',
  noGuardTimeExceeded = 'Превышено время нахождения поста без охраны',
  unknownPin = 'Введен неверный пинкод - ',
  wrongPin = 'Неправильный формат пинкода',
}

export type TLog = keyof typeof log


export interface IManagerLog {
  created_at: string,
  guarded_object_id: string,
  id: string,
  manager_id: string,
  security_guard_id: string,
  type: string
  value_1: any
}

export interface ILog {
  id: string
  date: number
  guardedObject: IGuardedObject
  securityGuard: ISecurityGuard
  type: TLog
}

interface IGuardedObject {
  id: string
  name: string
  shortNameHOS: null
  phone: string
}

interface ISecurityGuard {
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

export interface ICurrentShift {
  id: string
  endDate: null
  guardedObject: IGuardedObject
  name: string
  shortNameHOS: null
  securityGuard: ISecurityGuard
  startDate: number
  status: string
}

export interface IPost {
  id: string
  dialingStatus: string
  queueEndTime: string
  sosStatus: string
  numberOfCallAttempts: string
  noGuardNotification: string
  noGuardFromTime: string
  entityId: string
  name: string
  phone: string
  nonStandardWork: INonStandardWork[]
  standardWork: IStandardWork[]
  createdAt: string
  updatedAt: string
  lastListCheck: ILastListCheck
  currentShifts: ICurrentShift[]
  log: ILog[]
  isCentral: boolean
}

export interface IIntervalWork {
  id: string
  salary: number
  createdAt: string
  updatedAt: string
  hours: IUnixInterval[]
}

export interface IStandardWork extends IIntervalWork {
  day: TDays
}

export interface INonStandardWork extends IIntervalWork {
  day: number
}

export interface IUnixInterval {
  from: number
  to: number
}

export interface IDayInterval<TInterval = IInterval> {
  salary: number
  times: TInterval[]
}

interface IDateInterval<TInterval = IInterval> extends IDayInterval<TInterval> {
  day: number | string
}

export interface IPostFormValues<TInterval = IUnixInterval> {
  name: string
  phone: string
  Mon: IDayInterval<TInterval>
  Tue: IDayInterval<TInterval>
  Wed: IDayInterval<TInterval>
  Thu: IDayInterval<TInterval>
  Fri: IDayInterval<TInterval>
  Sat: IDayInterval<TInterval>
  Sun: IDayInterval<TInterval>
  nonStandardWork: IDateInterval<TInterval>[]
}
