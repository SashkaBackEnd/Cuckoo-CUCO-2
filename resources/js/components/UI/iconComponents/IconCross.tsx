import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconCross = (props: IconProps) => (
  <Icon viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <path d="M2 2l12 12M2 14L14 2" stroke="#8C8C8C" strokeWidth={2} />
  </Icon>
)

export default SvgIconCross
