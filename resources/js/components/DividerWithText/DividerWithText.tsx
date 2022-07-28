import { Box, Text } from '@chakra-ui/react'
import React from 'react'


export const DividerWithText: React.FC = ({ children }) => {

  return (
    <Box
      backgroundColor="#F5F5F5"
      height={8}
      w={"full"}
    >
      <Text
        fontWeight="400"
        pt={1}
        pl={2}
      >
        {children}      </Text>

    </Box>
  )
}
