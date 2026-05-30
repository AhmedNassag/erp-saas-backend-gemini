import axios from 'axios'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default class API {
  constructor(route) {
    this.route = route
  }

  async getAll(params = {}) {
    const response = await axios.get(this.route, { params })
    return response.data.data
  }

  async show(id) {
    const response = await axios.get(`${this.route}/${id}`)
    return response.data
  }

  async insert(data) {
    const response = await axios.post(this.route, data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    return response.data
  }

  async update(id, data) {
    data.append('_method', 'PUT')
    const response = await axios.post(`${this.route}/${id}`, data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    return response.data
  }

  async updatePut(id, data) {
    const response = await axios.put(`${this.route}/${id}`, data)
    return response.data
  }

  async delete(id) {
    const response = await axios.delete(`${this.route}/${id}`)
    notify({ text: 'Deleted Successfully', type: 'success' })
    return response.data
  }

  async exportAllToExcel() {
    const response = await axios.get(`${this.route}/export`, {
      responseType: 'blob'
    })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `${this.route}.xlsx`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }
}
