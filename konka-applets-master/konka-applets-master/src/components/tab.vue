<template>
    <div class="tabs">
        <div :class="['tab-item', {'active': activeKey===each.key}, {'isKActive': isK}]" v-for="(each, n) in tabs" :key="n" @click="changeTab(each.key)">
            <span>{{each.name}}</span>
            <div class="active-line"></div>
        </div>
    </div>
</template>
<script>
  export default {
    props: {
      defaultTab: {
        type: String,
        default () {
          return ''
        }
      },
      tabs: {
        type: Array,
        default () {
          return []
        }
      },
      isK: {
        type: Boolean,
        default: false
      }
    },
    data () {
      return {
        activeKey: ''
      }
    },
    computed: {},
    watch: {
      defaultTab (val, oldVal) {
        this.activeKey = val
      }
    },
    mounted: function () {
      let that = this
      that.$nextTick(() => {
        that.activeKey = that.defaultTab
      })
    },
    methods: {
      changeTab (key = '') {
        // if (!key) {
        //   return
        // }
        this.activeKey = key
        this.$emit('change', this.activeKey)
      }
    }
  }
</script>
<style lang="scss" scoped>
    .tabs {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;
        @extend .white-bg;
        /*padding: 0 30px;*/
        box-sizing: border-box;
    }

    .tab-item {
        @extend .elastic-flex-item;
        color: $black;;
        @extend .text-center;
        @extend .position-relative;
        padding: 30px 0;
        font-size: 28px;
        box-sizing: padding-box;
        transition: all 0.1s linear;
    }

    .tab-item.active {
        color: $theme_color;
    }

    .tab-item .active-line {
        @extend .position-absolute;
        left: 50%;
        bottom: 0;
        width: 100%;
        height: 4px;
        background-color: transparent;
        transform: translate(-50%, 100%);
        transition: all 0.1s linear;
    }

    // K币相关
    .isKActive.tab-item {
      .active-line {
        width: 26px;
        height: 6px;
      }
    }
    .isKActive.tab-item.active {
      span {
        color: #000000;
        font-size: 32px;
      }
    }

    .tab-item.active .active-line {
        background-color: $theme_color;
        transform: translate(-50%, 0);
    }
</style>

