import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconLeftArrow = (props: IconProps) => (
  <Icon viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <path d="M10 3L5 8l5 5" stroke="#8C8C8C" strokeWidth={1.4} strokeLinecap="round" strokeLinejoin="round" />
  </Icon>
)

export default SvgIconLeftArrow
