import service from '@/seraphine/actions/actions.js'


export const getapi=() => service.post("/open_api",{})
