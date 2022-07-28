import {chakra, theme as baseTheme, createStandaloneToast, extendTheme, ListItem} from '@chakra-ui/react'
import {UseToastOptions} from '@chakra-ui/toast/dist/types/use-toast'

export const Card = chakra('div', {
  baseStyle: {
    shadow: 'none',
    rounded: 0,
    bg: 'white',
    p: 10,
  },
})

export const NavItem = chakra(ListItem, {
  baseStyle: {
    color: 'white',
    px: 7,
    py: 3,
    w: '100%',
    _hover: {
      bg: '#434343',
    },
  },
})

export const ItemList = chakra('div', {
  baseStyle: {
    shadow: 'none',
    rounded: 0,
    transition: '145ms ease-in-out',
    bg: 'white',
    p: 6,
    mb: 3,
    _active: {
      shadow: 'inset 4px 0 0 #3E74F4, 0 4px 12px rgba(0, 0, 0, 0.1)',
    },
    _last: {
      mb: 0,
    },
    _hover: {
      shadow: 'inset 4px 0 0 #3E74F4, 0 4px 12px rgba(0, 0, 0, 0.1)',
    },
  },
})

export const Page = chakra('div', {
  baseStyle: {
    shadow: 'none',
    rounded: 0,
    px: [6, 6, 12, 12, 12, 24],
    pb: 7,
  },


})

export const PageBody = chakra('div', {
  baseStyle: {
    shadow: 'none',
    rounded: 0,
    bgColor: 'bgGray',
  },

})

const Button = {
  baseStyle: {
    fontWeight: 'bold',
    borderRadius: 'base',
  },
  sizes: {
    lg: {
      fontSize: 'sm',
      px: 6,
      py: 4,
    },
  },
  defaultProps: {
    size: 'lg',
  },
}

const Badge = {
  baseStyle: {
    fontWeight: 'bold',
    borderRadius: 'base',
    display: 'flex',
    width: 'fit-content',
    textTransform: 'none',
  },
  sizes: {
    sm: {
      fontSize: 'xs',
      px: 2,
      py: 1,
      mb: 2,
    },
  },
  defaultProps: {
    variant: 'solid',
    size: 'sm',
  },
}

const Divider = {
  baseStyle: {
    my: 4,
    borderColor: 'gray.500',
    opacity: 1,
  },
}

const Menu = {
  parts: ['list', 'item'],
  baseStyle: {
    list: {
      border: 0,
      boxShadow: 'lg',
      rounded: 0,
    },
    item: {
      _hover: {
        cursor: 'pointer',
        textColor: 'black',
      },
    },
  },
  defaultProps: {
    size: 'md',
  },
}

const Input = {
  baseStyle: {},
  sizes: {
    lg: {
      field: {
        fontSize: 'md',
        padding: 4,
        borderRadius: 0,
      },
    },
  },
  defaultProps: {
    size: 'lg',
  },
}

const Textarea = {
  baseStyle: {},
  sizes: {
    lg: {
      fontSize: 'md',
      padding: 4,
      borderRadius: 0,
    },
  },
  defaultProps: {
    size: 'lg',
  },
}

const Link = {
  baseStyle: {
    color: 'blue.500',
    textDecor: 'none',
    fontSize: 'sm',
  },
}

const FormLabel = {
  baseStyle: {
    color: 'label',
    fontSize: 'sm',
  },
}

const Text = {
  baseStyle: {
    fontSize: 'sm',
    color: 'blackText',
  },
}

const fonts = {
  body: `'Roboto', sans-serif`,
  heading: `'Roboto', sans-serif`,
  mono: `'Roboto', sans-serif`,
}

export const theme = extendTheme({
  components: {
    FormLabel,
    Button,
    Badge,
    Menu,
    Divider,
    Input,
    Textarea,
    Link,
    Text,
  },
  fonts,
  colors: {
    blackText: '#2C2C2C',
    label: '#8E8E8E',
    boxDividerColor: '#8C8C8C',
    boxDividerBg: '#F5F5F5',
    iconGray: '#D8D8D8',
    bgGray: '#F7F8F9',
    blue: {
      '50': '#E7EEFE',
      '100': '#BBCEFB',
      '200': '#90AFF9',
      '300': '#6590F6',
      '400': '#3971F4',
      '500': '#3E74F4',
      '600': '#0B41C1',
      '700': '#083191',
      '800': '#052061',
      '900': '#031030',
    },
    green: {
      '50': '#ECF9ED',
      '100': '#C9EECC',
      '200': '#A6E2AC',
      '300': '#84D78B',
      '400': '#61CC6B',
      '500': '#4CC557',
      '600': '#329A3B',
      '700': '#25742D',
      '800': '#194D1E',
      '900': '#0C270F',
    },
    red: {
      '50': '#FFECE5',
      '100': '#FFC9B8',
      '200': '#FFA68A',
      '300': '#FF8A00',
      '400': '#ff602e',
      '500': '#FF6230',
      '600': '#CC3100',
      '700': '#992500',
      '800': '#661900',
      '900': '#330C00',
    },
    gray: {
      '50': '#F2F2F2',
      '100': '#DBDBDB',
      '200': '#C4C4C4',
      '300': '#ADADAD',
      '400': '#969696',
      '500': '#EDEDED',
      '600': '#666666',
      '700': '#4D4D4D',
      '800': '#333333',
      '900': '#1A1A1A',
    },
  },
  baseTheme,
})

export const toast = (props: UseToastOptions): void => {
  const t = createStandaloneToast({theme})
  t({
    position: 'top-right',
    isClosable: true,
    duration: 3000,
    status: 'success',
    ...props,
  })
}
