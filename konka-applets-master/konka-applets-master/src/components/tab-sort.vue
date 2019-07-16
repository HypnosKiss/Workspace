<template>
    <div class="sort">
        <div :class="['sort-type', {'active': activeKey===each.key}]" v-for="(each, n) in sorts" :key="n" @click="changeTab(each.key)">
            <div class="sort-name">{{each.name}}</div>
            <div class="order-identify">
                <img class="order-identify-image" :src="ascImage" mode="aspectFit" v-if="activeKey===each.key && orderType==='asc'" />
                <img class="order-identify-image" :src="descImage" mode="aspectFit" v-if="activeKey===each.key && orderType==='desc'" />
            </div>
        </div>
    </div>
</template>
<script>
  export default {
    props: {
      sortKey: {
        type: String,
        default () {
          return ''
        }
      },
      defaultOrderType: {
        type: String,
        default () {
          return 'asc'
        }
      }
    },
    data () {
      return {
        activeKey: '',
        orderType: '',
        ascImage: require('@static/image/zhengxu.svg'),
        descImage: require('@static/image/fanxu.svg'),
        sorts: [
          {
            key: 'general',
            name: '综合'
          },
          {
            key: 'score',
            name: '评分'
          },
          {
            key: 'price',
            name: '价格'
          },
          {
            key: 'sales',
            name: '销量'
          }
        ]
      }
    },
    computed: {},
    watch: {
      sortKey (val, oldVal) {
        this.activeKey = val
      },
      defaultOrderType (val, oldVal) {
        this.orderType = val
      }
    },
    mounted: function () {
      let that = this
      that.$nextTick(() => {
      })
    },
    methods: {
      changeTab (key = '') {
        if (!key) {
          return
        }
        this.activeKey = key
        this.orderType = this.orderType === 'asc' ? 'desc' : 'asc'
        this.$emit('change', this.activeKey, this.orderType)
      }
    }
  }
</script>
<style lang="scss" scoped>
    .sort {
        @extend .row-flex;
        @extend .align-item-center;
        width: 100%;
        padding: 30px 0;
        background-color: $white;
        border-bottom: 1px solid $border_color;
    }
    .sort-type {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .justify-center;
        color: $black;;
        width: 25%;
        font-size: 28px;
    }
    .sort-type.active {
    }
    .active .sort-name {
        color: $red;
    }
    .order-identify {
        line-height: 0;
    }
    .order-identify-image {
        max-width: 20px;
        height: 25px;
        margin-left: 16px;
    }
</style>

