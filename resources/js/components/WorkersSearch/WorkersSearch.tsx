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
import { saveAs } from 'save-as'
import axios from 'axios'

import { Input } from '../UI/Input'
import { Card, toast } from '../../theme'
import { Icons } from '../UI/iconComponents'
import { TAxiosRefetch } from '../../models/axios'
import { errorHandler } from '../../errors'
import { IWorker } from '../../models/worker'
import { usePermissions } from '@hooks/usePermissions'
import { ROUTE_NAMES } from '@app/Routes'


interface IWorkersSearchProps {
  // refetch: TAxiosRefetch<IWorker[]>
  refetch?: () => void
  handleSearch: (string) => void

}


export const WorkersSearch: React.FC<IWorkersSearchProps> = (props) => {
  const { refetch, handleSearch } = props

  const [inputVal, setInputVal] = useState<string>("")

  useEffect(() => {
    handleSearch(inputVal)
  }, [inputVal])

  const { isEdit } = usePermissions(ROUTE_NAMES.workers)

  const importHandler = (files) => {
    const formData = new FormData()
    formData.append('file', files[0])

    axios.post(`/api/workers/import`, formData).then(() => {
      refetch()
      toast({
        title: 'Работники импортированы',
      })
    }).catch((error) => {
      errorHandler(error)
    })
  }

  const exportHandler = () => {
    axios.get(`/api/workers/export`, { responseType: 'blob' }).
      then(({ data }) => {
        const blob = new Blob(
          [data], { type: 'application/vnd.ms-excel;charset=utf-8' })
        saveAs(blob, `workers.xls`)
      }).
      catch((error) => {
        errorHandler(error)
      })
  }

  return (
    <Card mb={4}>
      <Flex mb={3} justifyContent="space-between">
        <Heading as="h4" size="lg">
          Работники
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
              Импорт всех работников
              <VisuallyHidden>
                <input
                  type="file"
                  multiple={false}
                  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                  onChange={(e) => importHandler(e.target.files)}
                />
              </VisuallyHidden>
            </MenuItem>
            <MenuItem onClick={exportHandler} type="button"
                      icon={<Icons.IconExport/>}>
              Экспорт всех работников
            </MenuItem>
          </MenuList>
        </Menu>
      </Flex>
      {isEdit && <Button
        as={Link}
        px={0}
        to={`/workers/create`}
        leftIcon={<Icons.IconPlus/>}
        colorScheme="grey"
        variant="ghost"
        mb={3}
      >
        Добавить работника
      </Button>}
      <InputGroup>
        <Input value={inputVal} onChange={(e) => setInputVal(e.target.value)} name="search" placeholder="Поиск"/>
        <InputRightElement>
          <Icons.IconSearch/>
        </InputRightElement>
      </InputGroup>
    </Card>
  )
}
