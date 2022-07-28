import * as React from 'react'
import {Icon, IconProps} from '@chakra-ui/react'

const SvgIconPvs = (props: IconProps) => (
  <Icon viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
    <path
      d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.6 0 12 0zm0 22.364A10.359 10.359 0 011.636 12 10.359 10.359 0 0112 1.636 10.359 10.359 0 0122.364 12 10.359 10.359 0 0112 22.364z"
      fill="currentColor"
    />
    <path
      d="M17.455 11.454h-4.91v-6a.839.839 0 00-.818-.818.839.839 0 00-.818.819v7.636H17.455a.839.839 0 00.818-.818.839.839 0 00-.818-.819z"
      fill="#000"
    />
  </Icon>
)

export default SvgIconPvs
