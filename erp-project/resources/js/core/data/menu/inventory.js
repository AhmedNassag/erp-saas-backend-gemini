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
  ],
}
