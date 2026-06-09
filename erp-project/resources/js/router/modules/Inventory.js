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
  {
    path: 'inventory/currencies',
    name: 'Currencies',
    component: () => import('../../views/Page/Inventory/Currencies.vue'),
    meta: { title: 'Currencies', module: 'Inventory' }
  },
  {
    path: 'inventory/units',
    name: 'Units',
    component: () => import('../../views/Page/Inventory/Units.vue'),
    meta: { title: 'Units', module: 'Inventory' }
  },
  {
    path: 'inventory/settings',
    name: 'Settings',
    component: () => import('../../views/Page/Inventory/Settings.vue'),
    meta: { title: 'Settings', module: 'Inventory' }
  },
  {
    path: 'inventory/products',
    name: 'Products',
    component: () => import('../../views/Page/Inventory/Products.vue'),
    meta: { title: 'Products', module: 'Inventory' }
  },
]
