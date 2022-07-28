import { IEvents } from '@models/events'
import moment from 'moment'


type TEventsObjectType =  {
  [keyOfAccum: string]: IEvents[]
}



export const  getDateAndMonth = (events: IEvents[]) => {

  return events.reduce<TEventsObjectType>((acc, curr): TEventsObjectType => {
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

