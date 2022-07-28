import {
  useHistory } from 'react-router-dom'


export const customReload = () => {
  const history = useHistory()
  history.push("/")
  window.location.reload()
}
