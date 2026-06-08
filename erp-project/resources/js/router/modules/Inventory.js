export default [
  {
    path: 'inventory/clients',
    name: 'Clients',
    component: () => import('../../views/Page/Inventory/Clients.vue'),
    meta: { title: 'Clients', module: 'Inventory' }
  },
  {
    path: 'inventory/providers',
    name: 'Providers',
    component: () => import('../../views/Page/Inventory/Providers.vue'),
    meta: { title: 'Providers', module: 'Inventory' }
  },
  {
    path: 'inventory/categories',
    name: 'Categories',
    component: () => import('../../views/Page/Inventory/Categories.vue'),
    meta: { title: 'Categories', module: 'Inventory' }
  },
  {
    path: 'inventory/brands',
    name: 'Brands',
    component: () => import('../../views/Page/Inventory/Brands.vue'),
    meta: { title: 'Brands', module: 'Inventory' }
  },
]
