import React, { memo } from 'react'
import { IManager } from '@models/manager'
import {
  Avatar,
  Button,
  ButtonGroup,
  IconButton,
  Td,
  Tr,
  Text,
  HStack
} from '@chakra-ui/react'
import { Icons } from '@components/UI/iconComponents'
import useAxios from 'axios-hooks'
import { managerAPI } from '@app/services'
import React_2 from 'framer-motion/dist/framer-motion'
import IconEyeDefault from '@components/UI/iconComponents/IconEyeDefault'



interface IManagersInAccessProps {
  manager: IManager,
  idx: number
}


export interface IAccessObj {
  logs: string | number
  objects: string | number
  reports: string | number
  workers: string | number
  managers: string | number
}


export const PermissionTypes = {
  edit: 'edit',
  view: 'view',
}

const ManagersInAccess: React.FC<IManagersInAccessProps> = ({ manager }) => {
  const { id, access: accessText, surname, name } = manager
  const [clickHandler] = managerAPI.useUpdateManagerPermissionsMutation()

  const access = JSON.parse(accessText)


  const setAccessValues = ( type: string, access: IAccessObj, key: string): IAccessObj => {
    if (access[key]) {
      access[key] = 0
      return access;
    }

    if (type === PermissionTypes.view) {
      access[key] = PermissionTypes.view
    } else if (type === PermissionTypes.edit) {
      access[key] = PermissionTypes.edit
    }
    return access
  }

  const ITEMS_TO_CLICK = {
    OBJECTS: "objects",
    MANAGERS: "managers",
    LOGS: "logs",
    WORKERS: "workers",
    REPORTS: "reports",
  }




  /**
   * handle permission access button clicks
   * You should pass type of button and item of section
   * @param type is type of icon (edit or view)
   * @param item is section ( ex. managers, objects, etc.)
   */
  const handleClick = (type: string, item: string) => {
    const updatedManagerData: IManager = {
      ...manager,
      access: JSON.stringify(setAccessValues(type, access, item)),
    }

    clickHandler(updatedManagerData)
  }

  return (
    <Tr>
      <Td> 
        <HStack>
        <Avatar size="sm" name={`${surname} ${name}`}/>
        <Text> {name} {surname}</Text>
        </HStack> 
      </Td> 
      <Td>
        <ButtonGroup>
          <Button
            p={0}
            onClick={() => handleClick(PermissionTypes.edit, ITEMS_TO_CLICK.OBJECTS)}
            isRound
            zIndex={1}
            size="sm"
            borderRadius="32px"
            as={IconButton}
            icon={ access?.objects === PermissionTypes.edit ? <Icons.IconEdit/> : <Icons.IconEditDefault/>}

          />
          <Button
            p={0}
            onClick={() => handleClick(PermissionTypes.view, ITEMS_TO_CLICK.OBJECTS)}
            isRound
            zIndex={1}
            size="sm"
            borderRadius="32px"
            as={IconButton}
            // colorScheme="gray"
            icon={ access?.objects === PermissionTypes.edit ? <Icons.IconEye/> : access?.objects === PermissionTypes.view ? <Icons.IconEye/> : <Icons.IconEyeDefault/>}


          />

        </ButtonGroup>

      </Td>

      <Td>
        <ButtonGroup>
          <Button
            p={0}
            onClick={() => handleClick(PermissionTypes.edit, ITEMS_TO_CLICK.MANAGERS)}
            isRound
            borderRadius="320px"
            zIndex={1}
            size="sm"
            as={IconButton}
            colorScheme="gray"
            icon={ access?.managers === PermissionTypes.edit ? <Icons.IconEdit/> : <Icons.IconEditDefault/>}


          />
          <Button
            p={0}
            onClick={() => handleClick(PermissionTypes.view, ITEMS_TO_CLICK.MANAGERS)}
            isRound
            zIndex={1}
            borderRadius="32px"
            size="sm"
            as={IconButton}
            colorScheme="gray"
            icon={ access?.managers === PermissionTypes.edit ? <Icons.IconEye/> : access?.managers === PermissionTypes.view ? <Icons.IconEye/> : <Icons.IconEyeDefault/>}


          />

        </ButtonGroup>

      </Td>

      <Td>
        <ButtonGroup>
          <Button
            p={0}
            onClick={() => handleClick(PermissionTypes.edit, ITEMS_TO_CLICK.WORKERS)}
            isRound
            zIndex={1}
            borderRadius="32px"
            size="sm"
            as={IconButton}
            colorScheme="gray"
            icon={ access?.workers === PermissionTypes.edit ? <Icons.IconEdit/> : <Icons.IconEditDefault/>}


          />
          <Button
            p={0}
            onClick={() => handleClick(PermissionTypes.view, ITEMS_TO_CLICK.WORKERS)}
            isRound
            zIndex={1}
            size="sm"
            borderRadius="32px"
            as={IconButton}
            colorScheme="gray"
            icon={ access?.workers === PermissionTypes.edit ? <Icons.IconEye/> : access?.workers === PermissionTypes.view ? <Icons.IconEye/> : <Icons.IconEyeDefault/>}


          />

        </ButtonGroup>

      </Td>

      <Td>
        <ButtonGroup>
            <Button
           variant={"unstyled"}
            p={0}
            onClick={() => handleClick(PermissionTypes.view, ITEMS_TO_CLICK.REPORTS)}
            isRound
            zIndex={1}
            borderRadius="32px"
            size="sm"
            as={IconButton}
            colorScheme="gray"
            icon={access?.reports === PermissionTypes.view ? <Icons.IconEye/> : <IconEyeDefault/>}
          />

        </ButtonGroup>

      </Td>

      <Td>
        <ButtonGroup>
          <Button
            p={0}
            onClick={() => handleClick(PermissionTypes.view, ITEMS_TO_CLICK.LOGS)}
            isRound
            zIndex={1}
            borderRadius="32px"
            size="sm"
            as={IconButton}
            colorScheme="gray"
            icon={access?.logs === PermissionTypes.view ? <Icons.IconEye/> : <IconEyeDefault/>}

          />

        </ButtonGroup>
      </Td>
    </Tr>

  )
}

export default memo(ManagersInAccess)
