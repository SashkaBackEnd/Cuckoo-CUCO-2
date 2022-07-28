import React, {Component} from 'react'
import {connect} from 'react-redux'
import {webSocketConnect} from '../../store/actions/websocket'
import {createNotification} from '../../store/actions/notifications'
import {checkObjectStatus, fetchObjectById, fetchObjects} from '../../store/actions/objects'
import {fetchGuardById, fetchGuards} from '../../store/actions/guards'

class WebSocketConnection extends Component {
  state = {
    socket: null
  }

  componentDidMount() {
    if (!this.props.connect) {
      const socketToken = localStorage.getItem('token')
      const socket = new WebSocket('wss://' + process.env.MIX_SOCKET_PATH + `?t=${socketToken}`)

      if (socket) {
        this.setState({
          socket: socket
        })
        this.props.webSocketConnect(socketToken)
      }
    }
  }

  render() {
    if (this.props.connect) {
      const socket = this.state.socket
      const $this = this
      if (socket) {
        socket.onclose = function (event) {
          if (event.wasClean) {
            console.log('Соединение закрыто чисто')
          } else {
            console.log('Обрыв соединения') // например, "убит" процесс сервера
          }
          console.log('Код: ' + event.code + ' причина: ' + event.reason)
        }

        socket.onmessage = function (event) {
          console.log('Получены данные ')
          const obj = JSON.parse(event.data)
          if (obj) {
            $this.props.createNotification('info', 'Обновление', 'Получены данные от сервера')
          }
          Object.keys(obj).map((key) => {
            switch (key) {
              case 'fetchObjectById':
                $this.props.fetchObjectById(obj[key], 'woStart', 'socket')
                break
              case 'fetchGuardById':
                $this.props.fetchGuardById(obj[key], 'woStart', 'socket')
                break
              case 'fetchObjects':
                $this.props.fetchObjects()
                break
              case 'fetchGuards':
                $this.props.fetchGuards()
                break
            }
          })
          if (event.data === 'good' || event.data === 'bad') {
            $this.props.createNotification('info', 'Получены данные', event.data)
          }
        }

        socket.onerror = function (error) {
          console.log('Ошибка ' + error.message)
        }
      }
    }

    return <React.Fragment>{this.props.children}</React.Fragment>
  }
}

function mapStateToProps(state) {
  return {
    connect: state.webSocket.connect
  }
}

function mapDispatchToProps(dispatch) {
  return {
    createNotification: (noty, title, text) => dispatch(createNotification(noty, title, text)),
    checkObjectStatus: (objectId, type, guardId) => dispatch(checkObjectStatus(objectId, type, guardId)),
    fetchObjects: () => dispatch(fetchObjects()),
    fetchGuards: () => dispatch(fetchGuards()),
    fetchObjectById: (id, type, socket) => dispatch(fetchObjectById(id, type, socket)),
    fetchGuardById: (id, type, socket) => dispatch(fetchGuardById(id, type, socket)),
    webSocketConnect: (socketToken) => dispatch(webSocketConnect(socketToken))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(WebSocketConnection)
