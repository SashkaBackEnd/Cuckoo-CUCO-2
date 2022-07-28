import React from 'react'
import {Box, BoxProps, Text} from '@chakra-ui/react'

export const BoxDivider: React.FC<BoxProps> = (props) => {
  const {children, ...rest} = props
  return (
    <Box px={4} py="10px" mb={4} mt={7} bg="boxDividerBg" {...rest}>
      <Text color="boxDividerColor">{children}</Text>
    </Box>
  )
}
