import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconWarning = (props: IconProps) => (
  <Icon viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <circle cx={15} cy={15} r={15} fill="#F5F4D9" />
    <path
      d="M21.619 20.495l-1.145-1.908a5.667 5.667 0 01-.808-2.916V14a4.673 4.673 0 00-3.333-4.47V8.333C16.333 7.598 15.735 7 15 7c-.736 0-1.334.598-1.334 1.333V9.53A4.673 4.673 0 0010.333 14v1.671a5.67 5.67 0 01-.807 2.916L8.38 20.495a.333.333 0 00.285.505h12.667a.334.334 0 00.286-.505zM12.9 21.667A2.33 2.33 0 0015 23a2.33 2.33 0 002.1-1.333h-4.2z"
      fill="#E5C657"
    />
  </Icon>
)

export default SvgIconWarning
