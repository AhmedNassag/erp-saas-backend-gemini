export function useMetronic() {
  function initComponents() {
    requestAnimationFrame(() => {
      if (window.KTApp) KTApp.init()
      if (window.KTMenu) KTMenu.createInstances()
      if (window.KTScroll) KTScroll.createInstances()
      if (window.KTDrawer) KTDrawer.createInstances()
      if (window.KTAppSidebar) KTAppSidebar.init()
    })
  }

  return { initComponents }
}
