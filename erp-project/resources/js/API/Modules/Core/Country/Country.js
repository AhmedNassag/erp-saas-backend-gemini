import API from "../../../API";
import axios from 'axios'

export default class Country extends API {
  constructor(route) {
    super(route)
  }

  async changeStatus(id, data) {
    const response = await axios.post(`${this.route}/change-status/${id}`, data)
    return response.data
  }
}
