<template>
    <div class="progress-bar" :style="{width: width + 'rpx', height: height + 'rpx', 'border-radius': borderRadius + 'rpx'}">
        <div class="progress" :style="{width: showingProgress + '%', 'border-radius': borderRadius + 'rpx', 'background-color': progressColor}"></div>
    </div>
</template>

<script>
  export default {
    props: {
      width: {
        type: Number,
        default () {
          return 100
        }
      },
      height: {
        type: Number,
        default () {
          return 12
        }
      },
      progress: {
        type: Number,
        default () {
          return 20
        }
      },
      progressColor: {
        type: String,
        default () {
          return '#ed1c24'
        }
      }
    },
    data () {
      return {
        showingProgress: 0
      }
    },
    computed: {
      borderRadius () {
        return this.height / 2
      },
      progressBorderRadius () {
        return (this.height - 2) / 2
      }
    },
    watch: {
      progress (val, oldVal) {
        this.setShowingProgress(val)
      }
    },
    methods: {
      mountedNextTick () {
        let that = this
        that.setShowingProgress(that.progress)
      },
      setShowingProgress (val = 0) {
        let that = this
        setTimeout(() => {
          that.showingProgress = val
        }, 200)
      }
    }
  }
</script>

<style lang="scss" scoped>
    .progress-bar {
        box-shadow: 0 0 1px 1px #cccccc inset;
        box-sizing: border-box;
        background-color: #f0f0f0;
    }
    .progress {
        height: 100%;
        transition: all 0.5s linear;
    }
</style>
