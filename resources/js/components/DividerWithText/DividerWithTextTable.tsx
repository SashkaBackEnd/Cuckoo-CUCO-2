import { Box, Text } from '@chakra-ui/react'
import React from 'react'


export const DividerWithTextTable: React.FC = ({ children }) => {

  return (
    <Box
      mt={'16px'}
      mb={'8px'}
      backgroundColor="#F5F5F5"
      height={8}
      w={"full"}
    >
      <Text
        p={'8px'}
        color={'#8C8C8C'}
        fontSize={'14px'}
        fontWeight="400"
        pl={2}
      >
        {children}      </Text>

    </Box>
  )
}
