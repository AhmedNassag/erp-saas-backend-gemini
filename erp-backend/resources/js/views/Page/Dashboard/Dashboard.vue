<template>
  <div class="row g-5 g-xl-10">
    <div class="col-xl-12 mb-5 mb-xl-10">
      <div class="card card-flush h-xl-100">
        <div class="card-header pt-7">
          <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-dark">Dashboard</span>
            <span class="text-gray-400 mt-1 fw-semibold fs-6">Welcome back, {{ userName }}</span>
          </h3>
        </div>
        <div class="card-body py-5">
          <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-md-4">
              <div class="card card-dashed h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center py-8">
                  <i class="ki-outline ki-building fs-3x text-primary mb-4"></i>
                  <span class="fs-2hx fw-bold text-gray-900">ERP System</span>
                  <span class="fs-6 text-gray-500">Core Module</span>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-dashed h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center py-8">
                  <i class="ki-outline ki-people fs-3x text-success mb-4"></i>
                  <span class="fs-2hx fw-bold text-gray-900">Active</span>
                  <span class="fs-6 text-gray-500">System Status</span>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-dashed h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center py-8">
                  <i class="ki-outline ki-security-user fs-3x text-info mb-4"></i>
                  <span class="fs-2hx fw-bold text-gray-900">{{ userName }}</span>
                  <span class="fs-6 text-gray-500">{{ userEmail }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="row g-5">
            <div class="col-12">
              <div class="card card-flush bg-light-primary">
                <div class="card-body d-flex align-items-center py-6">
                  <div class="d-flex flex-column">
                    <span class="fs-6 fw-bold text-gray-900">Quick Links</span>
                    <div class="mt-3">
                      <router-link to="/core/countries" class="btn btn-sm btn-primary me-2 mb-2">Countries</router-link>
                      <router-link to="/core/cities" class="btn btn-sm btn-primary me-2 mb-2">Cities</router-link>
                      <router-link to="/core/areas" class="btn btn-sm btn-primary me-2 mb-2">Areas</router-link>
                      <router-link to="/core/branches" class="btn btn-sm btn-primary me-2 mb-2">Branches</router-link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Auth } from '../../../API/Auth'

export default {
  name: 'DashboardView',
  computed: {
    userName() { return Auth.USER?.full_name || Auth.USER?.name || 'User' },
    userEmail() { return Auth.USER?.email || '' },
  }
}
</script>
