<template>


    <div class="container">

        <div class="sidebar">
            <h3>🔧 小工具</h3>
            <div style="margin-left: 15px; margin-bottom: 20px;">
                <el-switch v-model="copy_with_base"></el-switch>
                <span style="margin-left: 5px">复制带base_url</span>
            </div>

            <h3>📑 API 导航</h3>
            <ul class="nav-list">
                <template v-for="(item, index) in api_list" :key="index">
                    <li class="nav-controller">
                        <span
                            class="nav-controller-title"
                            :class="{ expanded: item.expanded }"
                            @click="toggle_nav(item)"
                        >
                            {{ item.name }}
                        </span>
                        <ul class="nav-methods" :class="{ show: item.expanded }">
                            <li v-for="(method, mIndex) in item.methods" :key="mIndex">
                                <a
                                    :href="'#method-' + item.name.toLowerCase() + '-' + method.name"
                                    @click.prevent="scroll_to_method(item.name, method.name)"
                                    :class="{ active: active_method === item.name.toLowerCase() + '-' + method.name }"
                                >
                                    {{ method.description }} <br/>{{ method.name }}()
                                </a>
                            </li>
                        </ul>
                    </li>
                </template>
            </ul>
        </div>


        <div class="main-content">
            <div class="header">
                <h1>📚 Seraphine API 接口文档</h1>
                <p>自动生成 · 实时同步 · 完整详细</p>
            </div>

            <div class="base-url-section">
                <div class="base-url-label">🌐 Base URL:</div>
                <div class="base-url-value">{{ baseurl }}</div>
                <button class="base-url-copy-btn" @click="copy_any(baseurl, $event)">📋 复制 Base URL</button>
            </div>

            <div class="base-url-section"
                 style="justify-content: start">
                <div style="font-weight: 900;color: #67C23A;width: 80px;">
                    请求方式
                </div>
                <div style="color: #E6A23C">
                    POST GET请求仅可在debug模式下使用
                </div>
            </div>
            <div class="base-url-section"
                 style="justify-content: start">
                <div style="font-weight: 900;color: #67C23A;width: 80px;">
                    请求头
                </div>
                <div style="color: #E6A23C">
                    Content-Type: application/json
                </div>
            </div>

            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number">{{ controller_num }}</div>
                    <div class="stat-label">控制器</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ api_num }}</div>
                    <div class="stat-label">API 接口</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ api_times }}</div>
                    <div class="stat-label">更新时间</div>
                </div>
            </div>

            <template v-for="(item,index) in api_list" :key="index">
                <div class="card" :id="item.name.toLowerCase()">
                    <h2>🔧{{ item.name }}</h2>
                    <p v-if="item.description" class="class-desc">{{ item.description }}</p>

                    <template v-for="(item2,index2) in item.methods" :key="index2">
                        <div class="method" :id="'method-' + item.name.toLowerCase() + '-' + item2.name">
                            <div class="method-header">
                                <div class="method-name">{{ item2.name }}()</div>
                                <div class="route-badge" @click="copy_any(item2.route, $event)">
                                    {{ item2.route }}
                                </div>
                            </div>

                            <p v-if="item2.description" class="method-desc">{{ item2.description }}</p>

                            <div v-if="item2.params && item2.params.length > 0" class="params-section">
                                <div class="params-title">📥 参数列表：</div>
                                <div v-for="(param, pIndex) in item2.params" :key="pIndex" class="param-item">
                                    <span class="param-type">{{ get_param_type(item2.params_desc, pIndex) }}</span>
                                    <span class="param-name">&nbsp;&nbsp;{{ param }}</span>
                                    <span v-if="get_param_desc(item2.params_desc, pIndex)" class="param-desc">
                                        - {{ get_param_desc(item2.params_desc, pIndex) }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="item2.example" class="example-section">
                                <div class="params-title">📝 请求示例：</div>
                                <div class="example-box" v-html="format_json(item2.example)"></div>
                                <button class="copy-btn" @click="copy_any(item2.example, $event)">📋 复制示例</button>
                            </div>


                            <div v-if="item2.return" class="return-section">
                                <span class="return-label">📤 返回：</span>{{ item2.return }}
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <button class="back-to-top" :class="{ show: show_back_top }" @click="scroll_to_top">↑</button>
    </div>
</template>

<script setup>
import {ref, onMounted, onUnmounted} from "vue"
import {getapi} from "@/seraphine/actions/apis.js";
import {ElMessage} from "element-plus";

const api_list = ref([])
const baseurl = ref("")
const controller_num = ref(0)
const api_num = ref(0)
const api_times = ref("0-0-0")
const active_method = ref("")
const show_back_top = ref(false)
const copy_with_base = ref(false)


import {useDark, useToggle} from '@vueuse/core'

const isDark = useDark()
const toggleDark = useToggle(isDark)


const get_api_data = () => {
    getapi({})
        .then((res) => {
            controller_num.value = 0
            api_num.value = 0
            console.log(res)
            api_list.value = res.api_list.map(item => ({
                ...item,
                expanded: false
            }))
            baseurl.value = res.base_url
            res.api_list.forEach((item) => {
                controller_num.value += 1
                item.methods.forEach(() => {
                    api_num.value += 1
                })
            })

            api_times.value = res.times
        })
}

onMounted(() => {
    get_api_data()
    window.addEventListener('scroll', handle_scroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handle_scroll)
})

const handle_scroll = () => {
    const cards = document.querySelectorAll('.card')
    let current = ''

    cards.forEach(card => {
        const cardTop = card.offsetTop
        if (window.pageYOffset >= (cardTop - 200)) {
            current = card.getAttribute('id')
        }
    })

    if (current) {
        api_list.value.forEach(item => {
            item.methods.forEach(method => {
                const methodId = item.name.toLowerCase() + '-' + method.name
                if ('method-' + methodId === current || item.name.toLowerCase() === current) {
                    active_method.value = methodId
                }
            })
        })
    }

    show_back_top.value = window.pageYOffset > 300
}

const toggle_nav = (item) => {
    item.expanded = !item.expanded
}

const scroll_to_method = (controller_name, method_name) => {
    const methodId = 'method-' + controller_name.toLowerCase() + '-' + method_name
    const target = document.getElementById(methodId)
    if (target) {
        target.scrollIntoView({behavior: 'smooth'})
        active_method.value = controller_name.toLowerCase() + '-' + method_name
    }
}

const scroll_to_top = () => {
    window.scrollTo({top: 0, behavior: 'smooth'})
}

const get_param_type = (params_desc, index) => {
    if (params_desc && params_desc[index]) {
        return params_desc[index].type || 'mixed'
    }
    return 'mixed'
}


const copy_any = (text, event) => {
    if (copy_with_base.value) {
        if (text !== baseurl.value) {
            text = baseurl.value + text
        }
    }

    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target
        const original_text = btn.textContent
        btn.textContent = '✅ 已复制'
        setTimeout(() => {
            btn.textContent = original_text
        }, 2000)


        ElMessage.success("复制成功")
    }).catch(err => {
        console.error('复制失败:', err)
        ElMessage.error("复制失败")
    })
}

const get_param_desc = (params_desc, index) => {
    if (params_desc && params_desc[index]) {
        return params_desc[index].desc || ''
    }
    return ''
}

const format_json = (json_str) => {
    if (!json_str) return ''

    try {
        let parsed
        if (typeof json_str === 'string') {
            parsed = JSON.parse(json_str)
        } else {
            parsed = json_str
        }

        const formatted = JSON.stringify(parsed, null, 2)

        return formatted.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+-]?\d+)?)/g, (match) => {
            let cls = 'json-number'
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'json-key'
                } else {
                    cls = 'json-string'
                }
            } else if (/true|false/.test(match)) {
                cls = 'json-boolean'
            } else if (/null/.test(match)) {
                cls = 'json-null'
            }
            return `<span class="${cls}">${match}</span>`
        })
    } catch (e) {
        console.error('JSON 解析失败:', e)
        return json_str
    }
}


</script>

<style>

html.dark body {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
}


.example-box {
    background: #0f172a;
    color: #e2e8f0;
    padding: 15px;
    border-radius: 6px;
    font-family: "Courier New", monospace;
    font-size: 0.9em;
    line-height: 1.6;
    overflow-x: auto;
    white-space: pre-wrap;
    word-wrap: break-word;
    border: 1px solid #334155;
    margin-top: 10px;
}

.example-box :deep(.json-key) {
    color: #F56C6C;
    font-weight: 600;
}

.example-box :deep(.json-string) {
    color: #34d399;
}

.example-box :deep(.json-number) {
    color: #fbbf24;
}

.example-box :deep(.json-boolean) {
    color: #f472b6;
    font-weight: 600;
}

.example-box :deep(.json-null) {
    color: #9ca3af;
    font-style: italic;
}


.return-section {
    margin-top: 15px;
    padding: 10px 15px;
    background: #1f2937;
    border-radius: 6px;
    border-left: 3px solid #fb923c;
    border: 1px solid #374151;
}

.return-label {
    color: #fb923c;
    font-weight: 600;
    margin-right: 10px;
}

.copy-btn {
    margin: 0;
    box-sizing: border-box;
    margin-top: 10px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9em;
    transition: all 0.3s ease;
}

.copy-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.route-badge {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-family: "Courier New", monospace;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
}

.route-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.example-box {
    background: #0f172a;
    color: #e2e8f0;
    padding: 15px;
    border-radius: 6px;
    font-family: "Courier New", monospace;
    font-size: 0.9em;
    line-height: 1.6;
    overflow-x: auto;
    white-space: pre-wrap;
    word-wrap: break-word;
    border: 1px solid #334155;
    margin-top: 10px;
}

.example-section {
    margin-top: 15px;
}

.params-section {
    margin-top: 15px;
}

.params-title {
    color: #60a5fa;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 0.95em;
}

.param-item {
    background: #1f2937;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 8px;
    font-family: "Courier New", monospace;
    font-size: 0.9em;
    border: 1px solid #374151;
}

.param-type {
    color: #60a5fa;
    font-weight: 600;
}

.param-name {
    color: #f87171;
}

.param-desc {
    color: #9ca3af;
    margin-left: 10px;
}

.method-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 10px;
}

.method-name {
    font-size: 1.3em;
    color: #e5e7eb;
    font-weight: 600;
}

.method-desc {
    color: #d1d5db;
    margin-bottom: 15px;
    line-height: 1.6;
}

.class-desc {
    color: #9ca3af;
    margin-bottom: 20px;
    line-height: 1.6;
}

.card {
    background: #1f2937;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    padding: 30px;
    margin-bottom: 30px;
    scroll-margin-top: 20px;
    border: 1px solid #374151;
}

.card h2 {
    color: #60a5fa;
    font-size: 1.8em;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 3px solid #60a5fa;
}

.method {
    background: #111827;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid #60a5fa;
    width: 100%;
}

.method:last-child {
    margin-bottom: 0;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    gap: 30px;
}

.sidebar {
    position: fixed;
    left: 20px;
    top: 40px;
    width: 250px;
    background: #1f2937;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    padding: 25px;
    max-height: calc(100vh - 80px);
    overflow-y: auto;
    z-index: 100;
    border: 1px solid #374151;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: #111827;
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

.sidebar h3 {
    color: #60a5fa;
    font-size: 1.2em;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #60a5fa;
}

.nav-list {
    list-style: none;
}

.nav-list li {
    margin-bottom: 8px;
}

.nav-controller {
    margin-bottom: 10px;
}

.nav-controller-title {
    display: block;
    padding: 10px 15px;
    color: #d1d5db;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-size: 0.95em;
    cursor: pointer;
    font-weight: 500;
    user-select: none;
}

.nav-controller-title:hover {
    background: #374151;
    color: #60a5fa;
}

.nav-controller-title::before {
    content: "▶";
    display: inline-block;
    margin-right: 8px;
    transition: transform 0.3s ease;
    font-size: 0.8em;
}

.nav-controller-title.expanded::before {
    transform: rotate(90deg);
}

.nav-methods {
    list-style: none;
    margin-left: 20px;
    margin-top: 5px;
    display: none;
}

.nav-methods.show {
    display: block;
}

.nav-methods li a {
    display: block;
    padding: 8px 15px;
    color: #9ca3af;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-size: 0.9em;
}

.nav-methods li a:hover {
    background: #374151;
    color: #60a5fa;
    transform: translateX(5px);
}

.nav-methods li a.active {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
}

.main-content {
    flex: 1;
    margin-left: 280px;
}

.header {
    text-align: center;
    color: #e0e0e0;
    margin-bottom: 40px;
    margin-top: 40px;
}

.header h1 {
    font-size: 2.5em;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.header p {
    font-size: 1.1em;
    opacity: 0.9;
}

.base-url-section {
    background: #1f2937;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    padding: 20px 30px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    border: 1px solid #374151;
}

.base-url-label {
    color: #60a5fa;
    font-weight: 600;
    font-size: 1.1em;
}

.base-url-value {
    flex: 1;
    background: #111827;
    padding: 12px 20px;
    border-radius: 8px;
    font-family: "Courier New", monospace;
    color: #e5e7eb;
    font-size: 1em;
    border: 2px solid #374151;
    min-width: 300px;
}

.base-url-copy-btn {
    padding: 12px 24px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.95em;
    font-weight: 500;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.base-url-copy-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.stats {
    background: #1f2937;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    padding: 20px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 20px;
    border: 1px solid #374151;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #60a5fa;
}

.stat-label {
    color: #9ca3af;
    font-size: 0.9em;
    margin-top: 5px;
}

.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
    transition: all 0.3s ease;
    z-index: 999;
}

.back-to-top:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
}

.back-to-top.show {
    display: flex;
}

@media (max-width: 1024px) {
    .sidebar {
        display: none;
    }

    .main-content {
        margin-left: 0;
    }

    .container {
        display: block;
    }

    .base-url-section {
        flex-direction: column;
        align-items: stretch;
    }

    .base-url-value {
        min-width: auto;
    }
}
</style>
