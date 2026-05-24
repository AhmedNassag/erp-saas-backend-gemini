import { publicApi } from './index'

export const portfolioApi = {
  getPackages:     ()             => publicApi.get('/portfolio/packages'),
  getSettings:     ()             => publicApi.get('/portfolio/settings'),
  getTestimonials: ()             => publicApi.get('/portfolio/testimonials'),
  getFeatures:     ()             => publicApi.get('/portfolio/features'),
  subscribe:       (packageId: number, data: object) =>
                     publicApi.post(`/portfolio/subscribe/${packageId}`, data),
  contact:         (data: object) => publicApi.post('/portfolio/contact', data),
}
