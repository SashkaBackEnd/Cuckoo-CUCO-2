import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconEmail = (props: IconProps) => (
  <Icon viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <circle cx={15} cy={15} r={15} fill="#E5E8ED" />
    <g clipPath="url(#icon-email_svg__clip0)" fill="#8C94A4">
      <path d="M17.584 16.015l5.35 3.381V12.49l-5.35 3.525zM7.067 12.49v6.906l5.349-3.381-5.35-3.525zM21.942 10.48H8.058a.98.98 0 00-.962.843L15 16.53l7.904-5.207a.98.98 0 00-.962-.844zM16.676 16.613l-1.403.925a.496.496 0 01-.546 0l-1.403-.925-6.226 3.938a.978.978 0 00.96.836h13.884a.978.978 0 00.96-.837l-6.226-3.937z" />
    </g>
    <defs>
      <clipPath id="icon-email_svg__clip0">
        <path fill="#fff" d="M8 8h14v14H8z" />
      </clipPath>
    </defs>
  </Icon>
)

export default SvgIconEmail
