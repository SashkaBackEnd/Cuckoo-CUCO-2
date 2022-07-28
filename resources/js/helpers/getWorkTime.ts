import {INonStandardWork, IStandardWork} from '@models/post'
import {TDays} from '@components/IntervalInput/IntervalInput'
import moment from 'moment'

export interface IWorkTime {
  time: string[]
  day: string
  salary: string
}

/* eslint-disable */
export const getWorkTime = (workIntervals: IStandardWork[] | INonStandardWork[]): IWorkTime | null => {
  const currDay = moment().locale('en').format('ddd') as TDays
  // @ts-ignore
  const dayInterval = workIntervals.find((workInterval) => workInterval.day === currDay)
  if (!dayInterval) {
    return null
  }
  const dayTime = dayInterval.hours.map(
    (interval) => `${moment(interval.from * 1000).format('HH:mm')}-${moment(interval.to * 1000).format('HH:mm')}`
  )
  return {salary: `${dayInterval.salary}/час`, time: dayTime, day: moment().format('ddd')}
}
