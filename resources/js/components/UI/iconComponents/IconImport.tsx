import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconImport = (props: IconProps) => (
  <Icon viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <path d="M12 0H0v1.882h12V0zm0 9.412H8.571V3.765H3.43v5.647H0L6 16l6-6.588z" fill="currentColor" />
  </Icon>
)

export default SvgIconImport
