import React from 'react'

import {Box, SlideFade} from '@chakra-ui/react'

export const PageTransition: React.FC = ({children}) => {
  return (
    <Box w="100%">
      <SlideFade in>{children}</SlideFade>
    </Box>
  )
}
