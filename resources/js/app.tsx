import React from 'react'

import {render} from 'react-dom'
import {BrowserRouter} from 'react-router-dom'
import {Provider} from 'react-redux'
import {ChakraProvider} from '@chakra-ui/react'
import moment from 'moment'

import {setupStore} from './store'
import Index from './Index'
import {theme} from './theme'

const store = setupStore()
moment.locale('ru')

render(
  <Provider store={store}>
    <ChakraProvider resetCSS theme={theme}>
      <BrowserRouter>
        <Index />
      </BrowserRouter>
    </ChakraProvider>
  </Provider>,
  document.getElementById('root')
)
