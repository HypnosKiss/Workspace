const express = require('express')
const consola = require('consola')
const { Nuxt, Builder } = require('nuxt')
const app = express()
const uuidv4 = require('uuid/v4')
const moment = require('moment')
const proxy = require('http-proxy-middleware')
const CryptoJs = require('crypto-js')
const apiPrefix = process.env.API_PREFIX || '/api'
const basePath = process.env.BASE_PATH || '/admin'
const rewritePath = '^' + basePath + apiPrefix
const rewriteRules = {}
rewriteRules[rewritePath] = apiPrefix

// Import and Set Nuxt.js options
const config = require('../nuxt.config.js')
config.dev = !(process.env.NODE_ENV === 'production')

async function start() {
  // Init Nuxt.js
  const nuxt = new Nuxt(config)

  const {
    host = process.env.HOST || '127.0.0.1',
    port = process.env.PORT || 3000
  } = nuxt.options.server
  app.use(
    basePath + apiPrefix,
    proxy({
      target: process.env.API_SERVER || 'https://kkypuat.konka.com',
      pathRewrite: rewriteRules,
      changeOrigin: true,
      onProxyReq: function(proxyReq, req) {
        const commonHeaders = {
          Accept: 'application/json',
          'Content-Type': 'application/json'
        }
        const method = req.method
        const bodyMd5 =
          req.headers.bodymd5 === undefined ? '' : req.headers.bodymd5
        const path = req.url
        let queryStr = ''
        const query = req.query ? req.query : {}
        if (Object.keys(query).length > 0) {
          Object.keys(query)
            .sort()
            .map(function(queryKey) {
              queryStr += queryKey + '=' + query[queryKey] + '&'
            })
        }
        queryStr =
          queryStr.length > 0
            ? '?' + queryStr.substr(0, queryStr.length - 1)
            : ''
        const signHeadersList = {
          'X-Version': 'v1',
          'X-Ca-Timestamp': moment()
            .utc()
            .format('YYYY-MM-DDTHH:mm:ss[Z]'),
          'X-Ca-Nonce': uuidv4(),
          'X-Request-Id': uuidv4(),
          'Content-MD5': bodyMd5
        }
        Object.keys(req.headers).map(function(headerKey) {
          if (headerKey.indexOf('X-') === 0) {
            signHeadersList[headerKey] = req.headers[headerKey]
          }
        })
        let headerStr = ''
        let signHeaders = ''
        Object.keys(signHeadersList)
          .sort()
          .map(function(header) {
            signHeaders += header + ','
            headerStr += header + ':' + signHeadersList[header] + '\n'
          })
        signHeaders = signHeaders.substr(0, signHeaders.length - 1)
        const signStr =
          method +
          '\n' +
          commonHeaders.Accept +
          '\n' +
          bodyMd5 +
          '\n' +
          commonHeaders['Content-Type'] +
          '\n' +
          signHeadersList['X-Ca-Timestamp'] +
          '\n' +
          headerStr +
          path +
          queryStr
        const headers = Object.assign(commonHeaders, signHeadersList)
        const signSecret = process.env.API_SECRET || 'abc1234567890'
        headers['X-Ca-Signature'] = CryptoJs.HmacSHA256(
          signStr,
          signSecret
        ).toString(CryptoJs.enc.Base64)
        headers['X-Ca-Signature-Headers'] = signHeaders
        Object.keys(headers).map(function(key) {
          proxyReq.setHeader(key, headers[key])
        })
      }
    })
  )

  // Build only in dev mode
  if (config.dev) {
    const builder = new Builder(nuxt)
    await builder.build()
  }

  // Give nuxt middleware to express
  app.use(nuxt.render)

  // Listen the server
  app.listen(port, host)
  consola.ready({
    message: `Server listening on http://${host}:${port}`,
    badge: true
  })
}
start()
