import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconChart = (props: IconProps) => (
  <Icon viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <g clipPath="url(#icon-chart_svg__clip0)" fill="#3A3A3A">
      <path d="M21.942 12.32H12.08V2.546a.648.648 0 00-.652-.641 11.156 11.156 0 00-7.899 3.23C1.428 7.25.27 10.02.27 12.935c0 6.084 5.005 11.033 11.156 11.033a11.152 11.152 0 007.897-3.23c2.102-2.116 3.26-4.873 3.26-7.79a.634.634 0 00-.64-.627zM18.4 19.848a9.847 9.847 0 01-6.976 2.844c-5.436 0-9.859-4.376-9.859-9.752 0-2.578 1.029-5.03 2.886-6.9a9.83 9.83 0 016.33-2.832v9.729c0 .354.285.668.643.668h9.837a9.809 9.809 0 01-2.861 6.243z" />
      <path d="M21.28 3.256c-.002-.002-.005.002-.008-.001C19.135 1.177 16.33.032 13.38.032a.648.648 0 00-.652.642v10.39c0 .355.297.615.655.615h10.508a.634.634 0 00.648-.619v-.009c0-2.916-1.157-5.68-3.259-7.795zm-7.254 7.14v-9.06a9.896 9.896 0 016.33 2.817c1.704 1.716 2.71 3.933 2.865 6.243h-9.195z" />
    </g>
    <defs>
      <clipPath id="icon-chart_svg__clip0">
        <path fill="#fff" d="M.27 0h24.27v24H.27z" />
      </clipPath>
    </defs>
  </Icon>
)

export default SvgIconChart