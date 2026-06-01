import axios from 'axios'

const baseURL = import.meta.env.VITE_API_URL || window.location.origin

export class Auth {
  static USER = null
  static PERMISSIONS = []

  static async logIn(email, password) {
    const response = await axios.post(`${baseURL}/api/login`, { email, password })
    const data = response.data
    if (data.token) {
      localStorage.setItem('api_token', data.token)
      localStorage.setItem('user_permissions', JSON.stringify(data.permissions || []))
      Auth.USER = data.user
      Auth.PERMISSIONS = data.permissions || []
    }
    return data
  }

  static getToken() {
    return localStorage.getItem('api_token')
  }

  static loggedIn() {
    return !!Auth.getToken()
  }

  static logOut() {
    localStorage.removeItem('api_token')
    localStorage.removeItem('user_permissions')
    Auth.USER = null
    Auth.PERMISSIONS = []
    window.location.href = '/login'
  }

  static run() {
    axios.defaults.baseURL = baseURL

    const perms = localStorage.getItem('user_permissions')
    if (perms) {
      Auth.PERMISSIONS = JSON.parse(perms)
    }

    axios.interceptors.request.use((config) => {
      const token = Auth.getToken()
      if (token) {
        config.headers.Authorization = `Bearer ${token}`
      }
      return config
    })
    axios.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          Auth.logOut()
        }
        return Promise.reject(error)
      }
    )
  }
}
