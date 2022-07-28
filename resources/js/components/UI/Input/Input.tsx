// React
import React from 'react'

// Third-party
import {FormControl, FormErrorMessage, FormLabel, forwardRef, Input, Textarea} from '@chakra-ui/react'
import {Control, Controller} from 'react-hook-form'
import InputMask from 'react-input-mask'

type InputElement = HTMLInputElement | HTMLTextAreaElement

/* eslint-disable */
interface IFormInputProps {
  name: string
  value?: string
  defaultValue?: string
  control?: Control<any, any>
  onChange?: (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => void
  label?: string
  id?: string
  maxLength?: number
  helperText?: string
  placeholder?: string
  autoFocus?: boolean
  type?: 'email' | 'password' | 'text' | 'tel' | 'hidden' | 'date' | 'datetime-local' | 'number' | 'textarea' | 'time'
  textarea?: boolean
  isRequired?: boolean
  disabled?: boolean
  error?: boolean
}

export const FormInput: React.FC<IFormInputProps & React.RefAttributes<InputElement>> = forwardRef((props, ref) => {
  const {isRequired, name, label, placeholder, textarea, helperText, error, type, ...rest} = props

  return (
    <FormControl isRequired={isRequired} isInvalid={error}>
      {label && <FormLabel fontSize={{base: "12px", md: "16px"}} htmlFor={name}>{label}</FormLabel>}
      {textarea ? (
        <Textarea placeholder={placeholder || label} name={name} {...rest} rows={3} ref={ref} />
      ) : type === 'tel' ? (
        <Controller
          name={name}
          {...rest}
          render={({field: {onChange, value, name, ref}}) => (
            <InputMask mask="+7(999)999-99-99" name={name} value={value} onChange={onChange}>
              {(inputProps) => <Input {...inputProps} type="tel" ref={ref} />}
            </InputMask>
          )}
        />
      ) : (
        <Input placeholder={placeholder || label} name={name} type={type} {...rest} ref={ref}  />
      )}
      {helperText && <FormErrorMessage>{helperText}</FormErrorMessage>}
    </FormControl>
  )
})
