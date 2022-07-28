import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconCircle = (props: IconProps) => (
  <Icon viewBox="0 0 200 200" {...props}>
    <path  fill="currentColor" d="M25 100a75 75 0 10150 0 75 75 0 10-150 0" />
  </Icon>
)

export default SvgIconCircle
