import React, { useEffect, useState } from 'react'
import { Heading } from '@chakra-ui/layout'
import {
  Button,
  Flex,
  IconButton,
  InputGroup,
  InputRightElement,
  Menu,
  MenuButton,
  MenuItem,
  MenuList,
  VisuallyHidden,
} from '@chakra-ui/react'
import { Link } from 'react-router-dom'
import { Input } from '../UI/Input'
import { Card, toast } from '../../theme'
import { Icons } from '../UI/iconComponents'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'


interface IWorkersSearchProps {
  // refetch: TAxiosRefetch<IWorker[]>
  refetch?: () => void,
  activeId: any
  handleSearch: (string) => void
}


export const ManagersSearch: React.FC<IWorkersSearchProps> = (props) => {
  const { handleSearch } = props
  const { isEdit } = usePermissions(ROUTE_NAMES.workers)
  const [inputVal, setInputVal] = useState<string>('')

  useEffect(() => {
    handleSearch(inputVal)
  }, [inputVal])

  return (
    <Card mb={4}>
      <Flex mb={3} justifyContent="space-between">
        <Heading as="h4" size="lg">
          Менеджеры
        </Heading>
        <Menu>
          <MenuButton
            isRound
            zIndex={1}
            size="sm"
            as={IconButton}
            aria-label="Опции"
            colorScheme="gray"
            icon={<Icons.IconDots/>}
            variant="outline"
          />
          <MenuList zIndex={1000}>
            <MenuItem as="label" icon={<Icons.IconImport/>}>
              Импорт всех менеджеров
              <VisuallyHidden>
                <input
                  type="file"
                  multiple={false}
                  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                />
              </VisuallyHidden>
            </MenuItem>
            <MenuItem type="button" icon={<Icons.IconExport/>}>
              Экспорт всех менеджеров
            </MenuItem>
          </MenuList>
        </Menu>
      </Flex>
      {isEdit && <Button
        as={Link}
        px={0}
        to={`/managers/create`}
        leftIcon={<Icons.IconPlus/>}
        colorScheme="grey"
        variant="ghost"
        mb={3}
      >
        Добавить менеджера
      </Button>}
      <InputGroup>
        <Input value={inputVal} onChange={e => setInputVal(e.target.value)}
               name="search" placeholder="Поиск"/>
        <InputRightElement>
          <Icons.IconSearch/>
        </InputRightElement>
      </InputGroup>
    </Card>
  )
}
