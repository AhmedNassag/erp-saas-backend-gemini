<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center text-center p-4">
    <div>
      <div class="text-8xl font-black text-slate-800 mb-4">404</div>
      <h1 class="text-2xl font-bold text-white mb-2">Page Not Found</h1>
      <p class="text-slate-400 text-sm mb-6">The page you are looking for does not exist.</p>
      <RouterLink to="/" class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold px-6 py-3 rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all">
        Go to Dashboard
      </RouterLink>
    </div>
  </div>
</template>