import React, { useEffect, useState } from 'react'

import { Button, InputGroup, InputRightElement } from '@chakra-ui/react'
import { Link } from 'react-router-dom'
import { Heading, Grid, Stack, Box, Flex, Wrap } from '@chakra-ui/layout'

import { Card } from '@app/theme'
import { Input } from '../UI/Input'
import { Icons } from '../UI/iconComponents'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'


interface IEntityPropsType {
  handleSearch: (string) => void
}


export const EntitySearch: React.FC<IEntityPropsType> = (props) => {
  const { isEdit } = usePermissions(ROUTE_NAMES.objects)
  const { handleSearch } = props
  const [inputVal, setInputVal] = useState<string>("")

  useEffect(() => {
    handleSearch(inputVal)
  }, [inputVal])


  console.log(inputVal, "inputVal")

  return (
    <Card mb={4}>
      <Heading as="h4" size="lg" mb={3}>
        Объекты
      </Heading>
      <Grid
        //  templateColumns="3fr 1fr"
        templateColumns={{ base: '3fr', md: '3fr 1fr' }}
        gap={{ base: '6', md: '32' }}
        alignItems="center"
        justifyContent="space-between"
      >
        <InputGroup>
          <Input value={inputVal} onChange={(e) => setInputVal(e.target.value)}
                 name="search" placeholder="Поиск по объектам"/>
          <InputRightElement>
            <Icons.IconSearch/>
          </InputRightElement>
        </InputGroup>
        {isEdit && <Button as={Link}
                           to="/entities/create"
                           type="submit"
                           colorScheme="blue"
                           variant="solid"
                           size="lg"
                           w="100%"
        >
          Добавить объект
        </Button>}
      </Grid>
    </Card>
  )
}
