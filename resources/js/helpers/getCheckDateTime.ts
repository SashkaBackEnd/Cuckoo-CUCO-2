import moment from 'moment'


export const getCheckDateTime = (dateTimeString: {date: number, type: string} | string): string => {
  if (typeof dateTimeString === 'string') {
    return dateTimeString
  } else {

    const {date, type} = dateTimeString

    const dateTimeInRussianArr = moment(date * 1000).format('LLL').split(' ')
    return "Проверено " + dateTimeInRussianArr[0] + ' ' + dateTimeInRussianArr[1] + ' ' +
      dateTimeInRussianArr[4]
  }


}
