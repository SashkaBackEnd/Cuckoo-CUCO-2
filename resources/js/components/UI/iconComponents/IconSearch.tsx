import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconSearch = (props: IconProps) => (
  <Icon viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <circle cx={7} cy={7} r={6.1} stroke="#878787" strokeWidth={1.8} />
    <path d="M12 12l3 3" stroke="#878787" strokeWidth={1.8} strokeLinecap="round" />
  </Icon>
)

export default SvgIconSearch
