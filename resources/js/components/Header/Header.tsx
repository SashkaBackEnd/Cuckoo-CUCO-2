import React from 'react'

import useAxios from 'axios-hooks'
import {LinkBox, LinkOverlay, Stack, Text} from '@chakra-ui/layout'
import {Link} from 'react-router-dom'

import {Loader} from '../UI/Loader'
import {events, IFastEvent} from '../../models/events'
import classes from './Header.module.css'

export const Header: React.FC = () => {
  const [{data, loading}] = useAxios<IFastEvent>({url: '/api/events/short', method: 'GET'})

  const date = new Date()

  return (
    <div className={classes.Header}>
      {(loading && !data) || !data ? (
        <Loader />
      ) : (
        <>
        <Stack direction='column' spacing='0.5rem' mb={4}
               // ml={{md: "232px"}}
        >
            <Text fontSize={{base:'14px',md:'16px'}} fontWeight='700'   color = '#fff' >
              События за{' '}
              {date.toLocaleDateString('ru-RU', {
                month: 'long',
                year: 'numeric',
                day: 'numeric',
              })}
            </Text>
            <Text fontSize={{base:'12px',md:'14px'}}  fontWeight='700'  color = '#8c8c8c'>Обновлено в {date.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})}</Text>
        </Stack>
          {/* <div className={classes.Date}>
            <span>
              События за{' '}
              {date.toLocaleDateString('ru-RU', {
                month: 'long',
                year: 'numeric',
                day: 'numeric',
              })}
            </span>
            <span>Обновлено в {date.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})}</span>
          </div> */}

          <div className={classes.Tabs}>
            <LinkBox as="div">
              <LinkOverlay as={Link} to="/reports">
                {events.countPostsWithoutGuards}
              </LinkOverlay>
              <span>{data.countPostsWithoutGuards}</span>
            </LinkBox>
            <LinkBox as="div">
              <LinkOverlay as={Link} to="/reports">
                {events.countDialingErrors}
              </LinkOverlay>
              <span>{data.countDialingErrors}</span>
            </LinkBox>
            <LinkBox as="div">
              <LinkOverlay as={Link} to="/reports">
                {events.countErrorsStartingJob}
              </LinkOverlay>
              <span>{data.countErrorsStartingJob}</span>
            </LinkBox>
            <LinkBox as="div">
              <LinkOverlay as={Link} to="/reports">
                {events.countErrorsFinishedJob}
              </LinkOverlay>
              <span>{data.countErrorsFinishedJob}</span>
            </LinkBox>
          </div>
        </>
      )}
    </div>
  )
}
