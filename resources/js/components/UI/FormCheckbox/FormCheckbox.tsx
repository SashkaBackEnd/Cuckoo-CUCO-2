// React
import {Switch} from '@chakra-ui/react'
import React from 'react'

// Styles
import classes from './FormCheckbox.module.css'

interface ICheckboxField {
  value?: 1 | 0
  onChange: (val: 1 | 0) => void
  label: string
  name: string
  id?: string
  classNameLabel?: string
  inputRef
}

const FormCheckbox: React.FC<ICheckboxField> = (props) => {
  const {label, value, id = 'id', name, onChange, classNameLabel, inputRef, ...rest} = props
  const [val, setVal] = React.useState(!!value)

  const handleChange = (e) => {
    const isChecked = e.target.checked ? 1 : 0
    setVal(e.target.checked)
    onChange(isChecked)
  }

  return (
    <div className={classes.input}>
      <label htmlFor={id + name}>
        <Switch
          mr={3}
          name={name}
          id={id + name}
          {...rest}
          ref={inputRef}
          isChecked={val}
          onChange={handleChange}
          size="lg"
        />
        <span className={classNameLabel ? classes[classNameLabel] : ''}>{label}</span>
      </label>
    </div>
  )
}

export default FormCheckbox
