import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconPencil = (props: IconProps) => (
  <Icon viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <g clipPath="url(#icon-pencil_svg__clip0)">
      <path
        d="M15.574 2.597L13.392.413c-.55-.55-1.506-.551-2.058.001l-9.774 9.87a.366.366 0 00-.092.157L.014 15.536a.364.364 0 00.45.45l5.09-1.456a.364.364 0 00.157-.091l9.863-9.784c.275-.275.426-.64.426-1.029 0-.389-.151-.754-.426-1.03zm-5.824.462l1.338 1.339L4.1 11.386l-.502-1.003a.364.364 0 00-.325-.201H2.69l7.06-7.123zM.893 15.107l.474-1.658 1.184 1.184-1.658.474zm4.198-1.2l-1.74.498-1.756-1.756.497-1.74h.956l.627 1.254c.035.07.092.127.162.162l1.254.627v.956zm.727-.597v-.583a.364.364 0 00-.2-.325L4.613 11.9l6.988-6.988 1.339 1.339-7.123 7.059zm9.243-9.16l-1.604 1.589-3.196-3.196L11.85.94a.744.744 0 011.029 0l2.182 2.182c.137.138.213.32.213.514a.721.721 0 01-.212.514z"
        fill="#C4C5C7"
      />
    </g>
    <defs>
      <clipPath id="icon-pencil_svg__clip0">
        <path fill="#fff" d="M0 0h16v16H0z" />
      </clipPath>
    </defs>
  </Icon>
)

export default SvgIconPencil
