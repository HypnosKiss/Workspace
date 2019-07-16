<template>
    <div :class="['page', {'iphone-x': isIpx}]">
        <div class="page-main">
            <div class="member-list">
                <div class="member" @click="goToOne">
                    <div class="serial-number red">①</div>
                    <div class="name">一级合伙人订单</div>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
                <div class="member" @click="goToTwo">
                    <div class="serial-number yellow">②</div>
                    <div class="name">二级合伙人订单</div>
                    <i class="icon iconfont icon-tiaozhuan- row-icon"></i>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
  import Basic from '@/mixins/basic.js'

  export default {
    mixins: [Basic],
    components: {
    },
    data () {
      return {
      }
    },
    computed: {
    },
    methods: {
      mountedNextTick () {
      },
      goToOne () {
        this.$utils.navigateTo('/pages/partner/my-member-orders/main')
      },
      goToTwo () {
        this.$utils.navigateTo('/pages/partner/my-sub-member-orders/main')
      }
    }
  }
</script>

<style lang="scss" scoped>
    .member-list {
        background-color: #ffffff;
        margin-top: 20px;
    }
    .total {
        height: 96px;
        line-height: 96px;
        padding: 0 30px;
        box-sizing: border-box;
        font-size: 28px;
        color: $black;
    }
    .member-list {
        padding-left: 30px;
        box-sizing: border-box;
        background-color: #ffffff;
    }
    .member {
        @extend .row-flex;
        @extend .align-item-center;
        @extend .border-bottom;

        height: 140px;
        padding-right: 30px;
        box-sizing: border-box;

        .serial-number {
            @extend .fixed-flex-item;
            @extend .row-flex;
            @extend .align-item-center;
            @extend .justify-center;

            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 40px;
            margin-right: 20px;
            text-align: center;
            font-size: 43px;
            color: #ffffff;
        }
        .serial-number.red {
            background-color: $theme_sub_color;
        }
        .serial-number.yellow {
            background-color: #d3c492;
        }
        .name {
            @extend .elastic-flex-item;
            margin-right: 20px;
        }
        .row-icon {
            @extend .fixed-flex-item;
            color: $light_black;
            font-size: 22px;
        }
    }
    .member:nth-last-of-type(1) {
        border-bottom: 0;
    }
</style>
