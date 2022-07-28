import React from 'react'
import {
  Center,
  CircularProgress,
  CircularProgressProps,
} from '@chakra-ui/react'
import { Box } from '@chakra-ui/layout'


export const Loader: React.FC<CircularProgressProps> = (props) => {
  return (
    <Center py={10}>

        <CircularProgress size={10} isIndeterminate
                          color="blue.300" {...props} />


    </Center>
  )
}
