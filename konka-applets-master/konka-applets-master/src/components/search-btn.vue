<template>
    <div class="search">
        <div class="search-btn" @click="onClick">
            <i class="icon iconfont icon-sousuo-"></i>
            <text class="search-keyword">{{placeholder}}</text>
        </div>
    </div>
</template>
<script>
  export default {
    props: {
      disabled: {
        type: Boolean,
        default: false
      },
      placeholder: {
        type: String,
        default: '搜索'
      }
    },
    data () {
      return {
      }
    },
    computed: {
    },
    mounted: function () {
      let that = this
      that.$nextTick(() => {
      })
    },
    methods: {
      onClick (e) {
        if (this.disabled) {
          return
        }
        this.$emit('click', e)
      }
    }
  }
</script>
<style lang="scss" scoped>
    .search {
        padding: 20px 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .search-btn {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .justify-center;
        height: 56px;
        border-radius: 28px;
        text-align: center;
        background-color: #f2f2f2;
    }
    .icon-sousuo- {
        font-size: 28px;
        color: #999999;
        padding-right: 15px;
    }
    .search-keyword {
        font-size: 24px;
        color: #999999;
        flex-shrink: 1;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

