import {ICurrentShift, ILastListCheck, ILog} from './post'

export type TWorkerStatus = 'служебный' | 'обычный'
export type TWorkType = 'смена' | 'вахта'
export type TWorkerLicenseRank = 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9

export interface IWorker {
  id: string
  pin: string
  name: string
  surname: string
  patronymic: string
  birthDate: number
  phone: string
  status: TWorkerStatus
  license: 1 | 0
  licenseRank: TWorkerLicenseRank
  licenseToDate: number
  drivingLicense: 1 | 0
  car: string
  workType: TWorkType
  medicalBook: string
  gun: string
  leftThings: string
  depts: string
  comment: string
  knewAboutUs: string
  lastListCheck: ILastListCheck
  log: ILog[]
  currentShift: ICurrentShift
}
