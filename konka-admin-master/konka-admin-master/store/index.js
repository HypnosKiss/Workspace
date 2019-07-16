export const state = () => ({
  menuDefinedList: [
    {
      name: '商品管理',
      icon: 'el-icon-goods',
      subMenuList: [
        {
          name: '在售商品管理',
          link: '/products/up-list',
          permissions: []
        },
        {
          name: '添加新商品',
          link: '/products/page',
          permissions: []
        },
        {
          name: '待售商品管理',
          link: '/products/down-list',
          permissions: []
        },
        {
          name: '商品回收站',
          link: '/products/recycle-list',
          permissions: []
        },
        {
          name: '商品评价管理',
          link: '/products/appraisal',
          permissions: []
        },
        {
          name: '商品分类管理',
          link: '/products/categories',
          permissions: []
        },
        {
          name: '商品规格管理',
          link: '/products/specification',
          permissions: []
        }
      ]
    },
    {
      name: '订单管理',
      icon: 'el-icon-tickets',
      subMenuList: [
        {
          name: '订单查询与跟踪',
          link: '/orders/list',
          permissions: []
        },
        {
          name: '退换货管理',
          link: '/orders/refund-list',
          permissions: []
        }
      ]
    },
    {
      name: '客服管理',
      icon: 'el-icon-service',
      subMenuList: [
        {
          name: '留言回复',
          link: '/customer-service/reply',
          permissions: []
        },
        {
          name: '聊天记录查询',
          link: '/customer-service/messages',
          permissions: []
        }
      ]
    },
    {
      name: '会员管理',
      icon: 'el-icon-mobile-phone',
      subMenuList: [
        {
          name: '会员列表',
          link: '/member/list',
          permissions: []
        }
      ]
    },
    {
      name: '合伙人管理',
      icon: 'el-icon-share',
      subMenuList: [
        {
          name: '合伙人列表',
          link: '/partners/list',
          permissions: []
        },
        {
          name: '佣金配置',
          link: '/partners/commission-settings',
          permissions: []
        }
      ]
    },
    {
      name: '营销管理',
      icon: 'el-icon-sold-out',
      subMenuList: [
        {
          name: '优惠卷管理',
          link: '/marketing/coupons',
          permissions: []
        }
      ]
    },
    {
      name: '内容管理',
      icon: 'el-icon-document',
      subMenuList: [
        {
          name: '广告管理',
          link: '/contents/advertising',
          permissions: []
        },
        {
          name: '单页管理',
          link: '/contents/page',
          power: []
        }
      ]
    },
    {
      name: '财务管理',
      icon: 'el-icon-printer',
      subMenuList: [
        {
          name: '提现管理',
          link: '/finance/withdraw',
          permissions: []
        },
        {
          name: '发票管理',
          link: '/finance/invoice',
          permissions: []
        }
      ]
    },
    {
      name: '人员管理',
      icon: 'el-icon-edit-outline',
      subMenuList: [
        {
          name: '角色管理',
          link: '/personnel/roles',
          permissions: []
        },
        {
          name: '管理员列表',
          link: '/personnel/list',
          permissions: []
        }
      ]
    },
    {
      name: '数据查询',
      icon: 'el-icon-search',
      subMenuList: [
        {
          name: '商品库存记录',
          link: '/data-table/inventory-record',
          permissions: []
        },
        {
          name: '佣金发放记录',
          link: '/data-table/commission-record',
          permissions: []
        }
      ]
    },
    {
      name: '系统管理',
      icon: 'el-icon-setting',
      subMenuList: [
        {
          name: '标签管理',
          link: '/system/tags',
          permissions: []
        },
        {
          name: '系统配置',
          link: '/system/setting',
          permissions: []
        }
      ]
    }
  ],
  isLoading: false,
  exportLoading: false
})
export const getters = {
  mainMenuList: state => {
    return state.menuDefinedList
  }
}
export const mutations = {
  beginLoading: state => {
    state.isLoading = true
  },
  endLoading: state => {
    state.isLoading = false
  },
  beginExport: state => {
    state.exportLoading = true
  },
  endExport: state => {
    state.exportLoading = false
  }
}
