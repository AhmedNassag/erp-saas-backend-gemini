import API from "../../../API";
import axios from 'axios'

export default class Product extends API {
  constructor(route) {
    super(route)
  }

  async changeStatus(id, data) {
    const response = await axios.post(`${this.route}/change-status/${id}`, data)
    return response.data
  }

  async byWarehouse(warehouseId) {
    const response = await axios.get(`${this.route}/warehouse/${warehouseId}`)
    return response.data
  }
}
