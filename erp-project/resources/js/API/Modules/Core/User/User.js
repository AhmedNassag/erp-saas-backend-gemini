import API from "../../../API";
import axios from 'axios'

export default class User extends API {
  constructor(route) {
    super(route)
  }

  async changeStatus(id, data) {
    const response = await axios.post(`${this.route}/change-status/${id}`, data)
    return response.data
  }

  async profile() {
    const response = await axios.get(`${this.route}/profile`)
    return response.data
  }
}
