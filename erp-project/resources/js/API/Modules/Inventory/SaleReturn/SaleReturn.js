import API from "../../../API";
import axios from 'axios'

export default class SaleReturn extends API {
  constructor(route) {
    super(route)
  }

  async downloadPdf(id, filename) {
    const response = await axios.get(`${this.route}/${id}/pdf`, { responseType: 'blob' })
    const blob = new Blob([response.data], { type: 'application/pdf' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = filename
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
  }
}
