<template>
  <div class="table-box">
    <el-table
      ref="eTable"
      :data="tableList"
      border
      style="width: 100%"
      empty-text="暂无数据"
      :loading="tableLoading"
      @selection-change="eventTransferSelectionChange"
    >
      <slot>
        &emsp;
      </slot>
    </el-table>
    <el-pagination
      :current-page="tablePage"
      :page-size="tablePageSize"
      layout="prev, pager, next"
      :total="tableRows"
      @current-change="switchPage"
    />
  </div>
</template>

<script>
import CryptoJs from 'crypto-js'
export default {
  name: 'TableList',
  props: {
    source: {
      type: String,
      required: true
    },
    searchKeywords: {
      type: Object,
      default: function() {
        return {}
      }
    },
    pageSize: {
      type: Number,
      default: 10
    },
    page: {
      type: Number,
      default: 1
    },
    autoLoad: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      tableLoading: false,
      tablePage: this.page,
      tablePageSize: this.pageSize,
      tableRows: 0,
      tableList: []
    }
  },
  mounted() {
    this.autoLoad === true && this.reload()
  },
  methods: {
    reload() {
      this.tablePage = 1
      this.tablePageSize = this.pageSize
      this.refresh()
    },
    refresh() {
      setTimeout(this.requestList, 20)
    },
    requestList() {
      this.$axios
        .get(this.source, {
          headers: {
            'X-Search-Keywords': CryptoJs.enc.Base64.stringify(
              CryptoJs.enc.Utf8.parse(JSON.stringify(this.searchKeywords))
            ),
            'X-Page': this.tablePage,
            'X-Per-Page': this.tablePageSize
          }
        })
        .then(response => {
          this.tableList = response.data.data
          this.tableRows = response.data.total
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    switchPage(page) {
      this.tablePage = page
      this.refresh()
    },
    eventTransferSelectionChange(event) {
      this.$emit('selection-change', event)
    }
  }
}
</script>

<style>
.table-box img {
  max-height: 50px;
  max-width: 100px;
}

.table-box .el-checkbox__inner {
  margin-left: 6px;
}
</style>
