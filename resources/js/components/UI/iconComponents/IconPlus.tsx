import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconPlus = (props: IconProps) => (
  <Icon viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <circle cx={10} cy={10} r={9} fill="#3E74F4" stroke="#3E74F4" strokeWidth={2} />
    <path d="M10 6.25v7.5M6.25 10h7.5" stroke="#fff" strokeWidth={2} strokeLinecap="round" />
  </Icon>
)

export default SvgIconPlus
