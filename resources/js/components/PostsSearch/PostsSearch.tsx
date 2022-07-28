import React from 'react'
import {Link} from 'react-router-dom'
import {Input} from '../UI/Input'
import {Heading} from '@chakra-ui/layout'
import {Button, InputGroup, InputRightElement} from '@chakra-ui/react'
import {Icons} from '../UI/iconComponents'
import {Card} from '../../theme'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'

interface IPostsSearchProps {
  entityId: string
}

export const PostsSearch: React.FC<IPostsSearchProps> = (props) => {
  const {isEdit} = usePermissions(ROUTE_NAMES.objects)
  const {entityId} = props
  return (
    <Card mb={4}>
      <Heading as="h4" size="md" mb={3}>
        Посты объекта:
      </Heading>
      { isEdit && <Button
        as={Link}
        px={0}
        to={`/entities/${entityId}/create`}
        leftIcon={<Icons.IconPlus/>}
        colorScheme="grey"
        variant="ghost"
        mb={3}
      >
        Добавить пост
      </Button>}
      <InputGroup>
        <Input name="search" placeholder="Поиск" />
        <InputRightElement>
          <Icons.IconSearch />
        </InputRightElement>
      </InputGroup>
    </Card>
  )
}
