import React from 'react'

import {Box, BoxProps} from '@chakra-ui/react'

import {Menu} from '../Navigation/Menu'

interface ILayoutProps extends BoxProps {
  isAuth?: boolean
}

export const Layout: React.FC<ILayoutProps> = (props) => {
  const {isAuth, children, ...rest} = props

  if (isAuth) {
    return (
      <Box h="full" w="full" mb={'63px'} bg="bgGray"  {...rest}>
        <Menu>{children}</Menu>
      </Box>
    )
  }

  return (
    <Box h="100vh" mb={'63px'} w="full" bg="bgGray" d="flex" flexDir="column" {...rest}>
      {children}
    </Box>
  )
}
