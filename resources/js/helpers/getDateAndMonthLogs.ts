
import moment from 'moment'
import { ILogs } from '@models/log'


export type TLogsObjectType =  {
  [keyOfAccum: string]: ILogs[]
}



export const  getDateAndMonthLogs = (logs: ILogs[]) => {

  return logs.reduce<TLogsObjectType>((acc, curr): TLogsObjectType => {
    const date: string = moment(+curr?.date * 1000).format('D MMMM HH:mm')
    const dateDayAndMonth: string [] = date.split(" ")
    const keyOfAccum: string = dateDayAndMonth[0] + " " + dateDayAndMonth[1]
    if (!acc.hasOwnProperty(keyOfAccum)) {
      acc[keyOfAccum] = [curr]
    } else {
      acc[keyOfAccum].push(curr)
    }
    return acc;
  }, {})

}
