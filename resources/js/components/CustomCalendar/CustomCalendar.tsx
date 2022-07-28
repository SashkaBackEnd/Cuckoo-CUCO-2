import React, { useEffect, useState } from 'react'

import * as locales from 'react-date-range/dist/locale';

import { DateRange } from 'react-date-range';
import 'react-date-range/dist/styles.css'; // main css file
import 'react-date-range/dist/theme/default.css';
import './DateRange.css'
import moment from 'moment'

interface ICalendarProps {
  handleChange: (startDate: number, endDate: number) => void

}

export const CustomCalendar: React.FC<ICalendarProps> = ({handleChange}) => {
  const [state, setState] = useState([
    {
      startDate: new Date(),
      endDate: null,
      key: 'selection'
    }
  ]);



  useEffect(() => {
    handleChange(moment(state[0].startDate).unix(), moment(state[0].endDate).unix())
  }, [state])



  return  <DateRange
    editableDateInputs={true}
    onChange={item => setState([item.selection])}
    moveRangeOnFirstSelection={false}
    ranges={state}
    locale={locales['ru']}

  />
}
