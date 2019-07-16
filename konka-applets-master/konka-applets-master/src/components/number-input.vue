<template>
    <div :class="componentClass">
        <div :class="['operate', canReduce ? 'active': 'disabled']" @click="reduce">
            <i class="icon iconfont icon-jianshao-moren-"></i>
        </div>
        <input type="number" :value="number" @input="onInput"/>
        <div :class="['operate', canAdd ? 'active': 'disabled']" @click="add">
            <i class="icon iconfont icon-zengjia-moren-"></i>
        </div>
    </div>
</template>
<script>
  export default {
    props: {
      disabled: {
        type: Boolean,
        default () {
          return false
        }
      },
      value: {
        type: Number,
        default () {
          return 0
        }
      },
      min: {
        type: Number,
        default () {
          return null
        }
      },
      max: {
        type: Number,
        default () {
          return null
        }
      },
      step: {
        type: Number,
        default () {
          return 1
        }
      },
      size: {
        type: String,
        default () {
          // return 'small'
          return 'standard'
        }
      }
    },
    data () {
      return {
        number: 0
      }
    },
    computed: {
      componentClass () {
        return ['number-input'].concat([this.size])
      },
      canReduce () {
        if (this.disabled) {
          return false
        }
        if ((this.min !== null) && ((this.number - 1) < this.min)) {
          return false
        }
        return true
      },
      canAdd () {
        if (this.disabled) {
          return false
        }
        if ((this.max !== null) && (this.max < (this.number + 1))) {
          return false
        }
        return true
      }
    },
    watch: {
      value (val, oldVal) {
        this.number = val || 0
      }
    },
    mounted: function () {
      let that = this
      that.$nextTick(() => {
        that.number = that.value || that.min || 0
      })
    },
    methods: {
      // 去除数字以外的字符
      onInput (e) {
        let value = this.$utils.getValue(e).replace(/[^\d|.]/g, '')
        if ((this.min !== null) && (value < this.min)) {
          value = this.min
        }
        if ((this.max !== null) && (this.max < value)) {
          value = this.max
        }
        return value
      },
      reduce () {
        if (this.disabled) {
          return
        }
        if (!this.canReduce) {
          return
        }
        this.number = this.number - this.step
        this.eventEmit()
      },
      add () {
        if (this.disabled) {
          return
        }
        if (!this.canAdd) {
          return
        }
        this.number = this.number + this.step
        this.eventEmit()
      },
      eventEmit () {
        this.$emit('input', this.number)
        this.$emit('change', this.number)
      }
    }
  }
</script>
<style lang="scss" scoped>
    .number-input {
        @extend .row-flex;
        @extend .align-item-stretch;
        border: 1px solid #cccccc;
        border-radius: 4px;
    }

    .operate {
        @extend .fixed-flex-item;
        @extend .row-flex;
        @extend .align-item-center;
        @extend .justify-center;
        /*width: 58px;*/
        /*height: 58px;*/
        /*text-align: center;*/
        background-color: #f0f0f0;
        overflow: hidden;
    }

    .operate.active {
        color: $black;
    }

    .operate.disabled {
        color: #e2e2e2;
    }

    .iconfont {
        /*font-size: 28px;*/
    }

    input {
        @extend .elastic-flex-item;
        /*height: 58px;*/
        /*line-height: 58px;*/
        text-align: center;
        /*font-size: 28px;*/
        color: $deep_black;
        box-sizing: border-box;
    }

    .small {
        .operate {
            width: 46px;
            height: 46px;
            /*line-height: 35px;*/
        }
        .iconfont {
            font-size: 23px;
        }
        input {
            min-height: 46px;
            height: 46px;
            line-height: 35px;
            font-size: 25px;
        }
    }

    .standard {
        .operate {
            width: 58px;
            height: 58px;
        }
        .iconfont {
            font-size: 28px;
        }
        input {
            min-height: 58px;
            height: 58px;
            line-height: 58px;
            font-size: 28px;
        }
    }
</style>

