
export default {
  data () {
    return {
      getListPath: null,
      perPage: 10,
      // 页码信息
      pageInfo: {},
      // 数据集
      listData: {},
      // 正在执行的列表请求
      listRequest: {},

      keywords: {},
      orderField: '',
      orderFieldMap: [],
      orderType: '',
      orderTypeMap: ['asc', 'desc']
    }
  },
  computed: {
    nowPageNum () {
      return this.pageInfo.currentPage || 0
    },
    list () {
      let result = []
      for (let n in this.listData) {
        result = result.concat(this.listData[n])
      }
      return result
    },
    showList () {
      return this.list
    },
    // 搜索条件定义
    searchCriteria () {
      return []
    },
    noSearchResult () {
      return this.pageInfo.total === 0
    },
    noMoreListData () {
      let nextNum = this.nowPageNum + 1
      // 判断有无分页信息
      if (!this.$utils.isEmptyObject(this.pageInfo)) {
        // 存在分页信息，则需要判断是否大于最后一页
        if (nextNum > parseInt(this.pageInfo.lastPage)) {
          // console.log('大于最后一页');
          console.log('无更多内容')
          return true
        }
      }
      return false
    }
  },
  methods: {
    // 滚动到了底部触发的函数
    startLoading () {
      // console.log('滚动到了底部触发的函数')
      this.getNextPage()
    },
    // 初始化数据
    defaultList () {
      this.resetPageInfo()
      this.resetKeywords()
    },
    resetPageInfo () {
      this.pageInfo = {}
      this.listData = {}

      // 终止所有旧的列表请求
      for (let n in this.listRequest) {
        this.deleteFromListRequest(n)
      }
    },
    resetKeywords () {
      this.keywords = this.defaultKeywords()
    },
    defaultKeywords () {
      return {}
    },
    changeKeyword (obj) {
      // console.log('接收到搜索条件')
      if (obj) {
        this.keywords = obj
      }
      this.resetPageInfo()
      this.startLoading()
    },
    // 请求下一页数据
    getNextPage () {
      console.log('mixins/list -> getNextPage 到底获取下一页')
      let that = this
      let nextNum = that.nowPageNum + 1
      // console.log(`下一页页码：${nextNum}`)

      // 判断有无分页信息
      if (!that.$utils.isEmptyObject(that.pageInfo)) {
        // 存在分页信息，则需要判断是否大于最后一页
        if (nextNum > parseInt(that.pageInfo.lastPage)) {
          // console.log('大于最后一页');
          console.log('无更多内容')
          return
        }
      }

      // 判断有无该页码的数据
      if (Object.keys(that.listData).indexOf(nextNum.toString()) > -1) {
        // 存在该页的数据则不进行请求
        // console.log('存在该页的数据则不进行请求');
        return
      }

      // 该页码的数据是否在请求中
      // if (Object.keys(that.listRequest).indexOf(nextNum.toString()) > -1) {
      if (that.listRequest[nextNum]) {
        // 在请求中不进行重复请求
        // console.log('在请求中不进行重复请求');
        return
      }
      // console.log(123)
      that.getList(nextNum)
    },
    // 获取列表数据
    getList (pageNum = 1) {
      let that = this
      // console.log('ak===>', !that.getLastPath)
      if (!that.getListPath) {
        // that.$utils.error('have no getListPath') by wzh 没有请求链接，不提示
        return
      }
      console.log('keywords====>', that.keywords)
      let xSearchKeywords = that.$utils.base64Encode(JSON.stringify(that.keywords))
      that.$utils.showLoading()
      let options = {
        method: 'get',
        url: that.getListPath,
        headers: {
          'X-Search-Keywords': xSearchKeywords,
          // 'X-Order-Field': that.orderField,
          // 'X-Order-Type': that.orderType,
          'X-Page': pageNum,
          'X-Per-Page': that.perPage
        }
      }

      let identification = (new Date()).getTime()
      console.log('getList identification ========> ', identification)
      that.$utils.requestServer(options, identification)
        .then(data => {
          // console.log(data)
          if (xSearchKeywords !== that.$utils.base64Encode(JSON.stringify(that.keywords))) {
            return
          }

          that.$utils.hideLoading()
          that.deleteFromListRequest(pageNum)
          that.changePageInfo(data)
          that.addToListData(pageNum, that.$utils.cloneObject(data.data))
        })
        .catch(err => {
          // console.log('this is catch')
          if (xSearchKeywords !== that.$utils.base64Encode(JSON.stringify(that.keywords))) {
            return
          }
          that.$utils.hideLoading()
          that.deleteFromListRequest(pageNum)
          that.$utils.error(err)
        })

      that.addToListRequest(pageNum, identification)
    },
    // 更改页码信息
    changePageInfo (data) {
      // console.log('更改页码信息')
      let pageInfo = this.$utils.cloneObject(data)
      delete pageInfo.data
      this.pageInfo = pageInfo
    },
    // 以页码为下标添加到数据集中
    addToListData (pageNum, data = []) {
      let newListData = this.$utils.cloneObject(this.listData)
      newListData[pageNum] = data
      this.listData = newListData
    },
    // 以页码为下标添加到当前请求集中
    addToListRequest (pageNum, identification) {
      if (!identification) {
        return
      }
      this.listRequest[pageNum] = identification
    },
    // 从当前请求集中删除下标为页码的请求
    deleteFromListRequest (pageNum) {
      let identification = this.listRequest[pageNum]
      delete this.listRequest[pageNum]
      console.log('从当前请求集中删除下标为页码的请求===>', pageNum, '======>', identification)
      this.$utils.deleteRequestingItem(identification)
    },
    // 变更排序条件
    changeOrder (type) {
      if (this.orderField === type) {
        // 变更为倒序或者顺序
        // console.log('变更为倒序或者顺序')
        if (this.orderType && this.orderType === this.orderTypeMap[0]) {
          this.orderType = this.orderTypeMap[1]
        } else {
          this.orderType = this.orderTypeMap[0]
        }
      } else {
        // console.log('变更排序条件')
        this.orderField = type
        this.orderType = this.orderTypeMap[0]
      }

      this.defaultList()
      this.startLoading()
    }
  }
}
