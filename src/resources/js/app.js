import { createApp, h } from 'vue'
import { createPinia } from "pinia";
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { useGlobalStore } from "./stores/global.js"
import '../css/app.css'

const pages = import.meta.glob('./pages/**/*.vue')
createInertiaApp({
  resolve: name => {
    const page = pages[`./pages/${name}.vue`]
    if (!page) throw new Error(`Página ${name} não encontrada`)
    return page()
  },
  setup({ el, App, props, plugin }) {
    const pinia = createPinia();

    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .mount(el)

    const themeStore = useGlobalStore()
    themeStore.setTheme(themeStore.theme)
  },
})