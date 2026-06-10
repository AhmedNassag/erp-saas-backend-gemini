export default {
  section: 'Core',
  items: [
    {
      title: 'Countries',
      icon: 'ki-outline ki-geolocation',
      route: '/core/countries',
      permission: 'read-country',
    },
    {
      title: 'Cities',
      icon: 'ki-outline ki-map',
      route: '/core/cities',
      permission: 'read-city',
    },
    {
      title: 'Areas',
      icon: 'ki-outline ki-map-pin',
      route: '/core/areas',
      permission: 'read-area',
    },
    {
      title: 'Branches',
      icon: 'ki-outline ki-shop',
      route: '/core/branches',
      permission: 'read-branch',
    },
    {
      title: 'Warehouses',
      icon: 'ki-outline ki-building',
      route: '/core/warehouses',
      permission: 'read-warehouse',
    },
    {
      title: 'Departments',
      icon: 'ki-outline ki-sitemap',
      route: '/core/departments',
      permission: 'read-department',
    },
    {
      title: 'Roles',
      icon: 'ki-outline ki-shield',
      route: '/core/roles',
      permission: 'read-role',
    },
    {
      title: 'Permissions',
      icon: 'ki-outline ki-lock',
      route: '/core/permissions',
      permission: 'read-permission',
    },
    {
      title: 'Users',
      icon: 'ki-outline ki-profile-user',
      route: '/core/users',
      permission: 'read-user',
    },
  ],
}
