import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconCancel = (props: IconProps) => (
  <Icon viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <circle cx={15} cy={15} r={15} fill="#E5E8ED" />
    <path
      d="M15 7c-4.411 0-8 3.589-8 8s3.589 8 8 8 8-3.589 8-8-3.589-8-8-8zm0 15.333c-4.044 0-7.333-3.29-7.333-7.333 0-4.044 3.29-7.333 7.333-7.333 4.044 0 7.333 3.29 7.333 7.333 0 4.044-3.29 7.333-7.333 7.333z"
      fill="#8C94A4"
    />
    <path
      d="M18.236 11.764a.333.333 0 00-.472 0L15 14.53l-2.764-2.765a.333.333 0 10-.472.472L14.53 15l-2.765 2.764a.333.333 0 10.472.472L15 15.47l2.764 2.765a.332.332 0 00.472 0 .333.333 0 000-.472L15.47 15l2.765-2.764a.333.333 0 000-.472z"
      fill="#8C94A4"
    />
  </Icon>
)

export default SvgIconCancel
