<template>
  <el-button
    :disabled="isLoading"
    type="success"
    plain
    round
    size="small"
    @click="exportList"
  >
    <slot>导出</slot>
  </el-button>
</template>

<script>
import CryptoJs from 'crypto-js'
import { mapState, mapMutations } from 'vuex'

export default {
  name: 'ExportButton',
  props: {
    exportUrl: {
      type: String,
      required: true
    },
    searchKeywords: {
      type: Object,
      default: function() {
        return {}
      }
    },
    selectRows: {
      type: Array,
      default: function() {
        return []
      }
    },
    exportMode: {
      validator: function(value) {
        return ['all', 'select'].indexOf(value) !== -1
      },
      default: 'all'
    }
  },
  data() {
    return {
      tipsBox: null
    }
  },
  computed: {
    ...mapState(['exportLoading']),
    isLoading() {
      return this.exportMode === 'select'
        ? this.selectRows.length <= 0 || this.exportLoading
        : this.exportLoading
    }
  },
  methods: {
    exportList() {
      this.beginExport()
      this.tipsBox = this.$notify({
        title: '导出excel',
        message: '系统正在处理中...',
        duration: 0,
        showClose: false,
        type: 'info'
      })
      const headers =
        this.exportMode === 'select'
          ? {
              'X-Select-Codes': CryptoJs.enc.Base64.stringify(
                CryptoJs.enc.Utf8.parse(JSON.stringify(this.selectRows))
              )
            }
          : {
              'X-Search-Keywords': CryptoJs.enc.Base64.stringify(
                CryptoJs.enc.Utf8.parse(JSON.stringify(this.searchKeywords))
              )
            }
      this.$axios
        .get(this.exportUrl, {
          headers: headers
        })
        .then(response => {
          this.endExport()
          this.tipsBox.close()
          window.location.href = response.data.fileUrl
        })
        .catch(error => {
          this.endExport()
          this.tipsBox.close()
          this.$message.error(error.message)
        })
    },
    ...mapMutations(['beginExport', 'endExport'])
  }
}
</script>

<style scoped></style>
