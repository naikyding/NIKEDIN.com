import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'
import storage from '../storage/index.js'

Vue.use(Vuex)
Vue.use(VueAxios, axios)

export default new Vuex.Store({
  strict: true,
  state: {
    slide: [],
    orderCart: [],
    errorText: '',
    user: {
      id: null
    },
    log: null,
    logIn: {
      userName: '',
      token: null
    }
  },
  mutations: {
    SLIDE_SELECT_SET (state, data) {
      state.slide = data
    },
    ORDER_CART_LIST_SET (state, data) {
      state.orderCart = data
    },
    overStock (state, data) {
      state.errorText = data
    },
    orderCartReset (state) {
      state.orderCart = []
    },
    userIdTrue (state, idState) {
      if (idState === false) state.user.id = false
      else state.user.id = true
    },
    ACC_Chk (state, data) {
      if (data) {
        state.logIn.userName = data.userName
        state.logIn.token = data.token
        state.log = true
      } else {
        state.log = false
      }
    },
    logOut_SET (state) {
      state.logIn.userName = ''
      state.logIn.token = null
      state.log = null
      state.user.id = null
      state.orderCart = []
    }
  },
  actions: {
    SLIDE_SELECT (context) {
      axios.get('/api/Github/ND_Vue/api/api.php?do=slide').then((res) => {
      // axios.get('api/api.php?do=slide').then((res) => {
        context.commit('SLIDE_SELECT_SET', res.data)
      })
    },
    ORDER_CART_LIST_GET (context) {
      const localData = storage.get('orderCart')
      if (localData === null) context.commit('orderCartReset')
      context.commit('ORDER_CART_LIST_SET', localData)
    },
    SET_ORDER_CART_LIST (context, data) {
      // // SET localStorage
      const localCartData = storage.get('orderCart')
      if (localCartData === null) {
        storage.set('orderCart', [data.list])
      } else {
        const sameItem = localCartData.filter(item => item.id === data.list.id && item.color === data.list.color && item.size === data.list.size && item.table === data.list.table)
        if (sameItem[0]) {
          sameItem[0].num = sameItem[0].num + data.list.num
          if (sameItem[0].num > data.inStock) {
            context.commit('overStock', '超過庫存量!!')
            return false
          }
          storage.set('orderCart', localCartData)
        } else {
          localCartData.push(data.list)
          storage.set('orderCart', localCartData)
        }
      }
    },
    numItemEv (context, data) {
      const cartData = storage.get('orderCart')
      const item = cartData[data.index]
      switch (data.ev) {
        case 'less':
          item.num--
          if (item.num < 1) return false
          storage.set('orderCart', cartData)
          break
        case 'plus':
          item.num++
          if (item.num > 10) return false
          storage.set('orderCart', cartData)
          break
        default:
          break
      }
    },
    REMOVE_ORDER_CART_ITEM (context, index) {
      storage.removeItem('orderCart', index)
    },
    accCheck (context, userId) {
      axios.post('/api/Github/ND_Vue/api/api.php?do=accCheck', JSON.stringify({ userId })).then((res) => {
      // axios.post('api/api.php?do=accCheck', JSON.stringify({ userId })).then((res) => {
        if (!res.data) context.commit('userIdTrue', false)
        else context.commit('userIdTrue', true)
      })
    },
    logIn (context, data) {
      axios.post('/api/Github/ND_Vue/api/api.php?do=logIn', JSON.stringify(data[0])).then((res) => {
      // axios.post('api/api.php?do=logIn', JSON.stringify(data[0])).then((res) => {
        context.commit('ACC_Chk', res.data)
        if (data[1]) storage.set('TOKEN', res.data.token)
        if (!res.data) return false
        const cartData = storage.get('orderCart')
        if (cartData) {
          const data = storage.get('orderCart')
          const list = {}
          data.forEach((item, index) => {
            list[index] = item
          })
          axios.post(`/api/Github/ND_Vue/api/api.php?do=SET_listToSession&user=${res.data.userName}`, JSON.stringify({ orderCart: list }), 'json').then((res) => {
          // axios.post(`api/api.php?do=SET_listToSession&user=${res.data.userName}`, JSON.stringify({ orderCart: list }), 'json').then((res) => {
          })
        } else {
          axios.get(`/api/Github/ND_Vue/api/api.php?do=GET_listToSession&user=${res.data.userName}`).then((res) => {
          // axios.get(`api/api.php?do=GET_listToSession&user=${res.data.userName}`).then((res) => {
            const list = res.data
            list.forEach(item => {
              item.img = item.img.split('_').join('.')
              item.title = item.title.split('_').join(' ')
            })
            storage.set('orderCart', list)
            context.commit('ORDER_CART_LIST_SET', list)
          })
        }
      })
    },
    logOut (context) {
      const cartData = storage.get('orderCart')
      if (cartData) {
        const data = storage.get('orderCart')
        const list = {}
        data.forEach((item, index) => {
          list[index] = item
        })
        axios.post(`/api/Github/ND_Vue/api/api.php?do=SET_listToSession&user=${this.state.logIn.userName}`, JSON.stringify({ orderCart: list }), 'json').then((res) => {
        // axios.post(`api/api.php?do=SET_listToSession&user=${this.state.logIn.userName}`, JSON.stringify({ orderCart: list }), 'json').then((res) => {
        })
      }
      storage.remove('TOKEN')
      storage.remove('orderCart')
      context.commit('logOut_SET')
    },
    rememberAcc (context, token) {
      axios.post('/api/Github/ND_Vue/api/api.php?do=rememberAcc', token).then((res) => {
      // axios.post('api/api.php?do=rememberAcc', token).then((res) => {
        if (!res.data) return false
        context.commit('ACC_Chk', { userName: res.data, token })
      })
    }
  },
  modules: {
  }
})
