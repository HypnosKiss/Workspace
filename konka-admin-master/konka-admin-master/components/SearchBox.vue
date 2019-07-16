<template>
  <div class="search-box" :class="{ more: showMore }">
    <div class="search-inline-box">
      <slot>&emsp;</slot>
    </div>
    <div class="search-more">
      <button @click="switchShowMore">
        <span>{{ showMoreText }}</span>
        <i class="el-icon-caret-bottom">&emsp;</i>
        <i class="el-icon-caret-top">&emsp;</i>
      </button>
    </div>
    <div class="search-btn">
      <el-button
        type="primary"
        size="small"
        plain
        round
        small
        @click="confirmSearch"
      >
        搜索
      </el-button>
      <el-button plain round size="small" @click="resetInput">
        重置
      </el-button>
      <slot name="btn">
        &emsp;
      </slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SearchBox',
  data() {
    return {
      showMore: false,
      showMoreText: '更多筛选项'
    }
  },
  methods: {
    switchShowMore() {
      this.showMore = !this.showMore
      this.showMoreText = this.showMore === false ? '更多筛选项' : '更少筛选项'
    },
    confirmSearch() {
      this.$emit('confirm-search')
    },
    resetInput() {
      this.$emit('reset-input')
    }
  }
}
</script>

<style>
.search-box .el-form-item {
  width: 25%;
  float: left;
  margin-bottom: 10px;
}

.search-inline-box {
  width: 100%;
  overflow: hidden;
  max-height: 100px;
}

.search-box.more .search-inline-box {
  max-height: 100%;
}

.search-btn {
  padding: 10px 0 30px;
  text-align: center;
}

.search-more {
  border-top: 1px solid rgb(235, 238, 245);
  position: relative;
  height: 30px;
}

.search-more button {
  outline: none;
  position: absolute;
  top: -1px;
  left: 50%;
  display: block;
  height: 28px;
  line-height: 28px;
  cursor: pointer;
  width: 100px;
  margin-left: -40px;
  font-size: 12px;
  border: 1px solid rgb(235, 238, 245);
  border-top: 1px solid #ffffff;
  background: #ffffff;
}

.search-more i {
  font-size: 0;
}

.search-more i:before {
  font-size: 12px;
}

.search-more .el-icon-caret-bottom {
  display: inline-block;
}

.search-more .el-icon-caret-top {
  display: none;
}

.search-box.more .el-icon-caret-bottom {
  display: none;
}

.search-box.more .el-icon-caret-top {
  display: inline-block;
}

.search-box .el-date-editor.el-input {
  width: 100%;
}
</style>
