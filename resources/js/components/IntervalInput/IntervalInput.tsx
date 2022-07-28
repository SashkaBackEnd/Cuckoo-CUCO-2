import React, { useCallback, useMemo } from 'react'
import classes from './IntervalInput.module.css'
import { IDayInterval } from '@models/post'
import { Box, Wrap, WrapItem } from '@chakra-ui/react'


/* eslint-disable */
export enum days {
  Mon = 'Понедельник',
  Tue = 'Вторник',
  Wed = 'Среда',
  Thu = 'Четверг',
  Fri = 'Пятница',
  Sat = 'Суббота',
  Sun = 'Воскресенье',
}


export type TDays = keyof typeof days


export interface IInterval {
  date: Date | string
  time: string
}


interface IIntervalNullable {
  date: null
  time: string
}


export const DEFAULT_INTERVAL = 30

const getTimeIntervals = (interval: number) => {
  const result: IInterval[] = []
  const start = new Date(1970, 1, 1, 0, 0)
  const end = new Date(1970, 1, 2, 0, 0)
  for (let d = start; d < end; d.setMinutes(d.getMinutes() + interval)) {
    result.push(format(d))
  }
  return result
}

export const format = (inputDate: Date) => {
  const date = new Date(inputDate)
  const hours = inputDate.getHours()
  const minutes = inputDate.getMinutes()
  return {
    date,
    time: `${hours
      ? (hours.toString().length === 1 ? `0${hours}` : hours)
      : '00'}:${minutes || '00'}`,
  }
}


interface IIntervalInputProps {
  day?: keyof typeof days
  date?: string
  isNotStandard?: boolean
  value?: IDayInterval
  interval?: number

  onChange(data: { times: IInterval[] }): void
}


interface IIntervalCheckboxProps extends IInterval {
  isHour: boolean
  checked?: boolean

  onChange(value: IInterval | IIntervalNullable): void
}


const IntervalCheckbox: React.FC<IIntervalCheckboxProps> = (props) => {
  const { time, onChange, date, isHour, checked } = props

  const handleChange = useCallback(
    (e) => {
      onChange(e.target.checked ? { date, time } : { date: null, time })
    },
    [onChange, date, time],
  )

  return (
    <label className={classes.input__input}>
      <input checked={checked} name={time} onChange={handleChange}
             type="checkbox"/>
      <p>{isHour ? <b>{time}</b> : time}</p>
      <span className={classes.input__checkmark}/>
    </label>
  )
}

export const IntervalInput: React.FC<IIntervalInputProps> = (props) => {
  const {
    day,
    value,
    onChange,
    isNotStandard = false,
    interval = DEFAULT_INTERVAL,
    children,
  } = props

  const intervals = useMemo(() => {
    return getTimeIntervals(interval)
  }, [interval])
  const hour = useMemo(() => {
    return 60 / interval
  }, [interval])

  const handleChange = useCallback(
    (val: IInterval) => {
      let newValue: IInterval[]
      if (val.date) {
        newValue = [...value.times, val]
      } else {
        newValue = [...value.times].filter(({ time }) => time !== val.time)
      }

      if (typeof onChange === 'function') {
        onChange({ ...value, times: newValue })
      } else {
        console.error('onChange is not a function')
      }
    },
    [onChange, value],
  )

  return (
    <div className={classes.input}>
      <div className={classes.input__header}>
        {!isNotStandard && <span>{day && days[day]}</span>}
        {children}
      </div>
      <Wrap>
        {intervals.map((interval, index) => {
          return (
            <WrapItem key={index}>
              <IntervalCheckbox
                key={interval.time}
                checked={!!value.times.find(
                  ({ time }) => time === interval.time)?.date}
                onChange={handleChange}
                isHour={index % hour === 0}
                {...interval}
              />
            </WrapItem>
          )
        })}
      </Wrap>
    </div>
  )
}
