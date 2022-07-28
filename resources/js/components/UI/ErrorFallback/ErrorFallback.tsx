import React from 'react'
import {ErrorBoundaryPropsWithFallback} from 'react-error-boundary'
import {Alert, AlertDescription, AlertIcon, AlertProps, AlertTitle, Button} from '@chakra-ui/react'

/* eslint-disable */

export const ErrorFallback: React.FC<ErrorBoundaryPropsWithFallback['FallbackComponent'] & AlertProps> = (props) => {
  const {error, resetErrorBoundary} = props
  return (
    <Alert
      status="error"
      variant="subtle"
      flexDirection="column"
      alignItems="center"
      justifyContent="center"
      textAlign="center"
      h="300px"
      p={10}
      {...props}
    >
      <AlertIcon boxSize={10} mb={5} />
      <AlertTitle mr={2}>Упс! Что-то пошло не так</AlertTitle>
      {/* @ts-ignore */}
      <AlertDescription mb={3}>{error?.message}</AlertDescription>
      <Button onClick={resetErrorBoundary} colorScheme="red" variant="outline">
        Перезагрузить
      </Button>
    </Alert>
  )
}
