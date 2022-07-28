import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconEntity = (props: IconProps) => (
  <Icon viewBox="0 0 24 21" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <path
      fillRule="evenodd"
      clipRule="evenodd"
      d="M11.537.811a.772.772 0 01.926 0l10.286 7.715a.771.771 0 01-.926 1.234l-1.337-1.003V19.43a.771.771 0 01-.772.771H4.286a.771.771 0 01-.772-.771V8.757L2.177 9.76a.771.771 0 11-.926-1.234L11.537.81zM12 2.393L18.943 7.6v11.057h-3.6v-6.086a.771.771 0 00-.772-.771H9.43a.771.771 0 00-.772.771v6.086h-3.6V7.6L12 2.393zm-1.8 16.264h3.6v-5.314h-3.6v5.314z"
      fill="currentColor"
    />
  </Icon>
)

export default SvgIconEntity
