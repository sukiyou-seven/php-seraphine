import 'element-plus/theme-chalk/dark/css-vars.css'
import "./style/dark/css-vars.css"

import { createApp } from 'vue'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import App from './App.vue'


const read_config = async () => {
    const response = await fetch("../../../config/app.json")
    return await response.json();
}

async function init() {
    let config = await read_config();
    localStorage.setItem("BASE_URL", config.base)
    // localStorage.setItem("platform_name", config.name)
}
init()



const app = createApp(App)

app.use(ElementPlus)
app.mount('#app')