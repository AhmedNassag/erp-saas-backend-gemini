export default [
  {
    path: 'core/countries',
    name: 'Countries',
    component: () => import('../../views/Page/Core/Countries.vue'),
    meta: { title: 'Countries', module: 'Core' }
  },
  {
    path: 'core/cities',
    name: 'Cities',
    component: () => import('../../views/Page/Core/Cities.vue'),
    meta: { title: 'Cities', module: 'Core' }
  },
  {
    path: 'core/areas',
    name: 'Areas',
    component: () => import('../../views/Page/Core/Areas.vue'),
    meta: { title: 'Areas', module: 'Core' }
  },
  {
    path: 'core/branches',
    name: 'Branches',
    component: () => import('../../views/Page/Core/Branches.vue'),
    meta: { title: 'Branches', module: 'Core' }
  },
  {
    path: 'core/departments',
    name: 'Departments',
    component: () => import('../../views/Page/Core/Departments.vue'),
    meta: { title: 'Departments', module: 'Core' }
  },
  {
    path: 'core/roles',
    name: 'Roles',
    component: () => import('../../views/Page/Core/Roles.vue'),
    meta: { title: 'Roles', module: 'Core' }
  },
  {
    path: 'core/permissions',
    name: 'Permissions',
    component: () => import('../../views/Page/Core/Permissions.vue'),
    meta: { title: 'Permissions', module: 'Core' }
  },
  {
    path: 'core/users',
    name: 'Users',
    component: () => import('../../views/Page/Core/Users.vue'),
    meta: { title: 'Users', module: 'Core' }
  },
]
