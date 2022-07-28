import moment from 'moment'


export const isTimeAgoSeconds = (time: number): boolean => moment(time * 1000).
  toNow(true) === 'несколько секунд'
