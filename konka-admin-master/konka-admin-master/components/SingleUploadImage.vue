<template>
  <el-upload
    class="upload-box"
    :action="uploadUrl"
    :multiple="false"
    :show-file-list="false"
    :before-upload="beforeUpload"
    :http-request="requestUpload"
    :on-success="previewImage"
    :on-progress="uploadProgress"
    accept="image/*"
    :style="{ height: boxHeight, 'line-height': boxHeight }"
  >
    <el-progress
      v-if="uploading"
      :width="150"
      type="circle"
      :percentage="uploadProgressPercentage"
    >
      上传完成
    </el-progress>
    <img
      v-if="imageUrl && !uploading"
      :style="{ 'max-height': uploadImageHeight }"
      :src="imageUrl"
      alt=""
      class="upload-image"
    />
    <div v-if="!imageUrl && !uploading" class="upload-text">
      <i class="el-icon-upload">&emsp;</i>
      <div>点击上传</div>
    </div>
    <div
      v-if="imageUrl && !uploading"
      class="upload-action"
      @click.self="selectImage"
    >
      <el-button type="primary">
        重新上传
      </el-button>
      <el-button type="danger" @click.stop="clearImage">
        删除
      </el-button>
    </div>
  </el-upload>
</template>

<script>
import axios from 'axios'
export default {
  name: 'SingleUploadImage',
  model: {
    prop: 'filename',
    event: 'upload-success'
  },
  props: {
    filename: {
      type: String,
      default: ''
    },
    initImage: {
      type: String,
      default: ''
    },
    imageHeight: {
      type: Number,
      default: 240
    }
  },
  data() {
    return {
      uploadUrl: '',
      uploadFilename: this.filename,
      uploadProgressPercentage: 0,
      uploading: false,
      imageUrl: this.initImage
    }
  },
  computed: {
    boxHeight() {
      return this.imageHeight + 2 + 'px'
    },
    uploadImageHeight() {
      return this.imageHeight + 'px'
    }
  },
  watch: {
    filename(newValue) {
      if (newValue === '') this.imageUrl = ''
    },
    initImage(newValue) {
      this.imageUrl = newValue
    }
  },
  methods: {
    beforeUpload() {
      return this.$axios.get('/admin/upload-url').then(response => {
        this.uploadFilename = response.data.filename
        this.uploadUrl = response.data.url
      })
    },
    requestUpload(config) {
      this.uploading = true
      this.uploadProgressPercentage = 0
      axios
        .put(config.action, config.file, {
          onUploadProgress: config.onProgress
        })
        .then(response => {
          this.uploading = false
          config.onSuccess(response)
        })
        .catch(error => {
          this.uploading = false
          config.onError(error)
        })
    },
    uploadProgress(e) {
      this.uploadProgressPercentage = Math.round((e.loaded / e.total) * 100)
    },
    previewImage(res, file) {
      this.imageUrl = URL.createObjectURL(file.raw)
      this.$emit('upload-success', this.uploadFilename)
    },
    clearImage() {
      this.imageUrl = ''
      this.$emit('upload-success', '')
    },
    selectImage(event) {
      event.stopPropagation()
    }
  }
}
</script>

<style scoped>
.upload-box {
  border: 1px solid #dcdfe6;
  border-radius: 3px;
  clear: both;
  position: relative;
  height: 242px;
  line-height: 242px;
  text-align: center;
  cursor: pointer;
}

.upload-box .el-upload {
  width: 100%;
}

.upload-box .el-icon-upload {
  font-size: 0;
}

.upload-box .el-icon-upload:before {
  font-size: 67px;
}

.upload-image {
  display: inline-block;
  vertical-align: middle;
  max-width: 98%;
}

.upload-text {
  display: inline-block;
  vertical-align: middle;
  height: 100px;
  line-height: 20px;
  width: 100%;
}
.el-progress {
  vertical-align: middle;
}
.upload-box:hover .upload-action {
  display: block;
}

.upload-action {
  position: absolute;
  display: none;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
}
</style>
