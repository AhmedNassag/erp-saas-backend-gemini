export default {
  section: 'Inventory',
  items: [
    {
      title: 'Clients',
      icon: 'ki-outline ki-profile-user',
      route: '/inventory/clients',
      permission: 'read-client',
    },
    {
      title: 'Providers',
      icon: 'ki-outline ki-profile-user',
      route: '/inventory/providers',
      permission: 'read-provider',
    },
    {
      title: 'Categories',
      icon: 'ki-outline ki-category',
      route: '/inventory/categories',
      permission: 'read-category',
    },
    {
      title: 'Brands',
      icon: 'ki-outline ki-brifecase-tick',
      route: '/inventory/brands',
      permission: 'read-brand',
    },
    {
      title: 'Currencies',
      icon: 'ki-outline ki-dollar',
      route: '/inventory/currencies',
      permission: 'read-currency',
    },
    {
      title: 'Units',
      icon: 'ki-outline ki-abstract-39',
      route: '/inventory/units',
      permission: 'read-unit',
    },
    {
      title: 'Products',
      icon: 'ki-outline ki-shield-tick',
      route: '/inventory/products',
      permission: 'read-product',
    },
    {
      title: 'Settings',
      icon: 'ki-outline ki-setting-2',
      route: '/inventory/settings',
      permission: 'read-setting',
    },
  ],
}
