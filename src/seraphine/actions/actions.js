import axios from 'axios'

let baseURL = "http://pay.rubyonly.cn"
baseURL = localStorage.getItem("BASE_URL") || "http://pay.rubyonly.cn"

const service = axios.create({
    baseURL: baseURL,
    timeout: 5000,
    headers: {
        'Content-Type': 'application/json'
    }
})

service.interceptors.request.use(
    config => {
        return config
    },
    error => {
        console.error('请求错误:', error)
        return Promise.reject(error)
    }
)

service.interceptors.response.use(
    response => {
        const res = response.data
        return res
    },
    error => {
        console.error('响应错误:', error.message || error)
        return Promise.reject(error)
    }
)

export default service
