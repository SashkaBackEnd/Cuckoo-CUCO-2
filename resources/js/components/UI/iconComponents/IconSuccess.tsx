import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconSuccess = (props: IconProps) => (
  <Icon viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <circle cx={15} cy={15} r={15} fill="#D9F5DD" />
    <g clipPath="url(#icon-success_svg__clip0)">
      <path
        d="M13.127 20.977a.815.815 0 01-1.155 0l-4.613-4.613a1.225 1.225 0 010-1.733l.578-.578a1.225 1.225 0 011.732 0l2.88 2.88 7.782-7.781a1.225 1.225 0 011.732 0l.578.577a1.225 1.225 0 010 1.733l-9.514 9.515z"
        fill="#4CC557"
      />
    </g>
    <defs>
      <clipPath id="icon-success_svg__clip0">
        <path fill="#fff" d="M7 7h16v16H7z" />
      </clipPath>
    </defs>
  </Icon>
)

export default SvgIconSuccess
