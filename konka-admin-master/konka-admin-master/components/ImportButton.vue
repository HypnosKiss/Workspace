<template>
  <span>
    <input
      ref="uploadFileInput"
      type="file"
      class="upload-file"
      @change="selectFile"
    />
    <el-button type="warning" size="mini" plain @click="uploadFile"
      >导入</el-button
    >
  </span>
</template>

<script>
export default {
  name: 'ImportButton',
  props: {
    rules: {
      type: Object,
      required: true
    }
  },
  data() {
    return { importTipsBox: null }
  },
  methods: {
    uploadFile() {
      this.$refs.uploadFileInput.click()
    },
    selectFile(event) {
      event.currentTarget.files.length > 0 &&
        this.readExcel(event.currentTarget.files[0])
      this.$refs.uploadFileInput.value = ''
    },
    readExcel(file) {
      const reader = new FileReader()
      reader.onload = function(e) {
        let binary = ''
        const bytes = new Uint8Array(e.target.result)
        const length = bytes.byteLength
        for (let i = 0; i < length; i++) {
          binary += String.fromCharCode(bytes[i])
        }
        const wb = XLSX.read(binary, { type: 'binary' })
        const wsname = wb.SheetNames[0]
        const ws = wb.Sheets[wsname]
        this.importTipsBox = this.$notify({
          title: '批量导入',
          message: '系统正在导入中...',
          duration: 0,
          showClose: false
        })
        this.requestImport(XLSX.utils.sheet_to_json(ws, { header: 'A' }))
      }.bind(this)
      reader.readAsArrayBuffer(file)
    },
    convertExcel(importJson) {
      importJson.shift()
      return importJson.map(
        function(line) {
          const newLine = {}
          Object.keys(this.rules).forEach(
            function(key) {
              newLine[this.rules[key]] =
                line[key] === undefined ? '' : line[key]
            }.bind(this)
          )
          return newLine
        }.bind(this)
      )
    },
    requestImport(importJson) {
      this.$axios
        .post('/admin/import/partners', this.convertExcel(importJson))
        .then(response => {
          this.importTipsBox.close()
          this.$message.success(response.message)
          this.$emit('success', response)
        })
        .catch(error => {
          this.importTipsBox.close()
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped>
.upload-file {
  display: none;
}
</style>
