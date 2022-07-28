import React from 'react'

import {FormControl, FormErrorMessage, FormLabel, forwardRef} from '@chakra-ui/react'
import {Select} from 'chakra-react-select'
import {Props} from 'react-select'
import {ControllerRenderProps} from 'react-hook-form'
import { log } from '@models/post'

export interface IOption {
  label: string
  value: string | number
}

// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore
interface IFormSelectProps extends ControllerRenderProps, Partial<Props> {
  options: IOption[]
  label: string
  placeholder?: string
  helperText?: string
  selectHandler?: any
}

export const CustomSelect: React.FC<IFormSelectProps> = forwardRef((props, ref) => {
  const {options, label, onChange, selectHandler, placeholder = 'Выбрать...', helperText, name, value, ...rest} = props

  const handleChange = React.useCallback(
    (option: IOption) => {


      if (typeof onChange === 'function') {
        onChange(option?.value || '')
      }
    },
    [onChange]
  )


  const activeOption = React.useMemo(() => options.find((item) => item.value === value), [value, options])

  return (
    <FormControl>
      {label && <FormLabel htmlFor={name}>{label}</FormLabel>}
      <Select
        {...rest}
        ref={ref}
        name={name}
        onChange={handleChange}
        defaultValue={activeOption}
        options={options}
        size="lg"
        placeholder={placeholder}
      />
      {helperText && <FormErrorMessage>{helperText}</FormErrorMessage>}
    </FormControl>
  )
})
