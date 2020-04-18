module.exports = {
  publicPath: './',
  devServer: {
    proxy: {
      '^/api/': {
        target: 'http://localhost:80',
        changeOrigin: true, // so CORS doesn't bite us. 
        pathRewrite: { // /api/ -> 代表 target
          '^/api': ''
        } 
      }
    }
  }
}