import moment from 'moment'


/**
 * checks if given date is in date range of first 2 arguments
 * @param startDate
 * @param endDate
 * @param currValue
 */
export const isDateInRange = (
  startDate: number, endDate: number, currValue: number): boolean => {

  if (!endDate) return true

  return moment(
      startDate).unix() <= moment(currValue).unix() && moment(endDate).unix() >=
    moment(currValue).unix()
}

