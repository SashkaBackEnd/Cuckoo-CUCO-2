import React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const IconErrrors = (props: IconProps) => (
  <Icon viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <circle xmlns="http://www.w3.org/2000/svg" cx="8.5" cy="8" r="8" fill="#FF6230"/>
    <path xmlns="http://www.w3.org/2000/svg" d="M5.5 5L11.5 11" stroke="white" strokeWidth="2"/>
    <path xmlns="http://www.w3.org/2000/svg" d="M5.5 11L11.5 5" stroke="white" strokeWidth="2"/>
  </Icon>
)

export default IconErrrors
