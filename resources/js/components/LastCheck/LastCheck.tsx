import React from 'react'

import { Stack, TextProps } from '@chakra-ui/layout'
import { ILastListCheck } from '@models/post'
import { Flex, HStack, Text, Wrap } from '@chakra-ui/react'
import moment from 'moment'

import { Icons } from '../UI/iconComponents'
import classes from './LastCheck.module.css'


interface ILastCheckProps extends TextProps {
  lastListCheck?: ILastListCheck
}


export const LastCheck: React.FC<ILastCheckProps> = (props) => {
  const { lastListCheck, ...rest } = props
  if (!lastListCheck) {
    return null
  }
  console.log('LLC', props)
  return (
    <HStack alignItems={'flex-start'}>

      <Text fontSize="12px"
        color={lastListCheck.type === 'good' ? '#4CC557' : 'red'} {...rest}
        className={classes.LastCheck}>
        <Icons.IconCircle mr={1} w={3} h={3} />

      </Text>

      <Flex direction={['column', 'column', 'row']} alignItems={'flex-start'} flexWrap={'wrap'}>

        <Text mt={'-3px'}
          mr={'3px'}
          fontSize="12px"
          color={lastListCheck.type == 'good'
            ? '#4CC557'
            : 'red'} {...rest}
          className={classes.LastCheck}> Проверено </Text>

        <Text

          mt={'-3px'}
          fontSize="12px"
          color={lastListCheck.type === 'good' ? '#4CC557' : 'red'} {...rest}
          className={classes.LastCheck}>

          {moment(lastListCheck.date * 1000).format('D MMMM HH:mm')}

        </Text>

      </Flex>


    </HStack>

  )
}
