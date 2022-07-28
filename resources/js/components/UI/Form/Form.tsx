// React
import React from 'react'

import {chakra, ChakraProps} from '@chakra-ui/react'

export const Form: React.FC<
  React.DetailedHTMLProps<React.FormHTMLAttributes<HTMLFormElement>, HTMLFormElement> & ChakraProps
> = (props) => {
  const {children, ...restProps} = props
  return (
    <chakra.form noValidate {...restProps}>
      {children}
    </chakra.form>
  )
}
