<template>
  <el-container>
    <el-header height="35px" style="border-bottom: 1px solid #ebeef5;">
      <span>合伙人列表</span>
      <span class="header-btn-box">
        <import-button :rules="importRules" @success="confirmSearch" />
        <el-button type="primary" size="mini" plain @click="createInfo"
          >新增</el-button
        >
      </span>
    </el-header>
    <el-main>
      <search-box @confirm-search="confirmSearch" @reset-input="resetSearch">
        <el-form label-position="right" label-width="120px">
          <el-form-item label="合伙人编码">
            <el-input v-model="inputSearchKeyword.code"></el-input>
          </el-form-item>
          <el-form-item label="激活码">
            <el-input v-model="inputSearchKeyword.activationCode"></el-input>
          </el-form-item>
          <el-form-item label="真实姓名">
            <el-input v-model="inputSearchKeyword.idName"></el-input>
          </el-form-item>
          <el-form-item label="身份证号码">
            <el-input v-model="inputSearchKeyword.idNumber"></el-input>
          </el-form-item>
          <el-form-item label="联系电话">
            <el-input v-model="inputSearchKeyword.clientPhone"></el-input>
          </el-form-item>
          <el-form-item
            v-if="
              inputSearchKeyword.type === '30' ||
                inputSearchKeyword.type === '40'
            "
            label="员工姓名"
          >
            <el-input v-model="inputSearchKeyword.inlineName"></el-input>
          </el-form-item>
          <el-form-item
            v-if="
              inputSearchKeyword.type === '30' ||
                inputSearchKeyword.type === '40'
            "
            label="工卡编号/电话"
          >
            <el-input v-model="inputSearchKeyword.inlineNumber"></el-input>
          </el-form-item>
          <el-form-item
            v-if="
              inputSearchKeyword.type === '30' ||
                inputSearchKeyword.type === '40'
            "
            label="一级部门"
          >
            <el-input v-model="inputSearchKeyword.firstDepartment"></el-input>
          </el-form-item>
          <el-form-item
            v-if="
              inputSearchKeyword.type === '30' ||
                inputSearchKeyword.type === '40'
            "
            label="二级部门"
          >
            <el-input v-model="inputSearchKeyword.secondDepartment"></el-input>
          </el-form-item>
          <el-form-item
            v-if="
              inputSearchKeyword.type === '30' ||
                inputSearchKeyword.type === '40'
            "
            label="三级部门"
          >
            <el-input v-model="inputSearchKeyword.thirdDepartment"></el-input>
          </el-form-item>
          <el-form-item
            v-if="
              inputSearchKeyword.type === '20' ||
                inputSearchKeyword.type === '50'
            "
            label="分公司"
          >
            <el-input v-model="inputSearchKeyword.companyName"></el-input>
          </el-form-item>
          <el-form-item v-if="inputSearchKeyword.type === '20'" label="R3编码">
            <el-input v-model="inputSearchKeyword.r3Code"></el-input>
          </el-form-item>
          <el-form-item
            v-if="inputSearchKeyword.type === '20'"
            label="客户名称"
          >
            <el-input v-model="inputSearchKeyword.clientName"></el-input>
          </el-form-item>
          <el-form-item
            v-if="inputSearchKeyword.type === '20'"
            label="客户类型"
          >
            <el-input v-model="inputSearchKeyword.clientType"></el-input>
          </el-form-item>
          <el-form-item
            v-if="inputSearchKeyword.type === '20'"
            label="区域信息"
          >
            <el-input v-model="inputSearchKeyword.area"></el-input>
          </el-form-item>
          <el-form-item
            v-if="inputSearchKeyword.type === '20'"
            label="合并编码"
          >
            <el-input v-model="inputSearchKeyword.mergeCode"></el-input>
          </el-form-item>
          <el-form-item
            v-if="inputSearchKeyword.type === '50'"
            label="经办名称"
          >
            <el-input v-model="inputSearchKeyword.handingName"></el-input>
          </el-form-item>
          <el-form-item
            v-if="inputSearchKeyword.type === '50'"
            label="网点编号"
          >
            <el-input v-model="inputSearchKeyword.networkCode"></el-input>
          </el-form-item>
          <el-form-item
            v-if="inputSearchKeyword.type === '50'"
            label="网点名称"
          >
            <el-input v-model="inputSearchKeyword.networkName"></el-input>
          </el-form-item>
          <el-form-item
            v-if="inputSearchKeyword.type === '50'"
            label="上级客户"
          >
            <el-input v-model="inputSearchKeyword.parentClientName"></el-input>
          </el-form-item>
          <el-form-item
            v-if="inputSearchKeyword.type === '50'"
            label="上级客户编码"
          >
            <el-input v-model="inputSearchKeyword.parentClientCode"></el-input>
          </el-form-item>
          <el-form-item
            v-if="
              inputSearchKeyword.type === '20' ||
                inputSearchKeyword.type === '50'
            "
            label="客户地址"
          >
            <el-input v-model="inputSearchKeyword.companyAddress"></el-input>
          </el-form-item>
          <el-form-item
            v-if="
              inputSearchKeyword.type === '20' ||
                inputSearchKeyword.type === '50'
            "
            label="业务员"
          >
            <el-input v-model="inputSearchKeyword.salesman"></el-input>
          </el-form-item>
          <el-form-item
            v-if="
              inputSearchKeyword.type === '20' ||
                inputSearchKeyword.type === '50'
            "
            label="业务员电话"
          >
            <el-input v-model="inputSearchKeyword.salesmanPhone"></el-input>
          </el-form-item>
          <el-form-item label="状态">
            <el-select
              v-model="inputSearchKeyword.status"
              placeholder="请选择状态"
            >
              <el-option label="全部" value=""></el-option>
              <el-option
                v-for="(option, index) in statusList"
                :key="index"
                :label="option.name"
                :value="option.code"
              ></el-option>
            </el-select>
          </el-form-item>
        </el-form>
        <template slot="btn">
          <export-button
            export-url="/admin/export/partner"
            :select-rows="selectRows"
            export-mode="select"
          >
            导出选中
          </export-button>
          <export-button
            export-url="/admin/export/partner"
            :search-keywords="searchKeywords"
            >导出全部
          </export-button>
        </template>
      </search-box>
      <el-tabs
        v-model="inputSearchKeyword.type"
        type="card"
        @tab-click="switchType"
      >
        <el-tab-pane label="全部" :name="'0'">
          <table-list
            ref="tableList"
            :search-keywords="searchKeywords"
            source="/admin/partners"
            :page-size="10"
            @selection-change="selectToRows"
          >
            <el-table-column type="selection"></el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="activationCode"
              label="激活码"
            ></el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="nickname"
              label="合伙人昵称"
            ></el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="code"
              label="合伙人编码"
            ></el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="clientPhone"
              label="联系电话"
            ></el-table-column>
            <el-table-column
              v-for="(column, columnIndex) in tableColumn(0)"
              :key="columnIndex"
              header-align="center"
              align="center"
              :label="column.label"
            >
              <template slot-scope="scope">
                {{ columnFormatter(scope.row, column) }}
              </template>
            </el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="statusText"
              label="状态"
            ></el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              label="操作"
              width="180"
            >
              <template slot-scope="scope">
                <el-button
                  type="text"
                  size="small"
                  @click="showDetailInfo(scope.row.code)"
                  >详情
                </el-button>
                <el-button
                  v-if="scope.row.type !== 10"
                  type="text"
                  size="small"
                  @click="updateInfo(scope.row.code)"
                >
                  编辑
                </el-button>
                <el-button
                  type="text"
                  size="small"
                  @click="confirmDelete(scope.row.code)"
                >
                  删除
                </el-button>
                <el-button
                  v-if="scope.row.type !== 10"
                  type="text"
                  size="small"
                  @click="confirmDowngradeInfo(scope.row.code)"
                >
                  降级
                </el-button>
                <el-button
                  v-if="scope.row.type === 10"
                  type="text"
                  size="small"
                  @click="upgradeInfo(scope.row.code)"
                >
                  升级
                </el-button>
                <el-button
                  type="text"
                  size="small"
                  @click="setTags(scope.row.code)"
                >
                  设置标签
                </el-button>
                <el-button
                  v-if="scope.row.status === 20"
                  type="text"
                  size="small"
                  @click="enableInfo(scope.row.code)"
                >
                  启用
                </el-button>
                <el-button
                  v-if="scope.row.status === 10"
                  type="text"
                  size="small"
                  @click="disableInfo(scope.row.code)"
                >
                  禁用
                </el-button>
              </template>
            </el-table-column>
          </table-list>
        </el-tab-pane>
        <el-tab-pane
          v-for="(tab, index) in typeList"
          :key="index"
          :label="tab.name"
          :name="String(tab.code)"
        >
          <table-list
            :ref="'tableList' + tab.code"
            :search-keywords="searchKeywords"
            source="/admin/partners"
            :page-size="10"
            @selection-change="selectToRows"
          >
            <el-table-column type="selection"></el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="activationCode"
              label="激活码"
            ></el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="nickname"
              label="合伙人昵称"
            ></el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="code"
              label="合伙人编码"
            ></el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="clientPhone"
              label="联系电话"
            ></el-table-column>
            <el-table-column
              v-for="(column, columnIndex) in tableColumn(tab.code)"
              :key="columnIndex"
              header-align="center"
              align="center"
              :label="column.label"
            >
              <template slot-scope="scope">
                {{ columnFormatter(scope.row, column) }}
              </template>
            </el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="statusText"
              label="状态"
            ></el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              label="操作"
              width="180"
            >
              <template slot-scope="scope">
                <el-button
                  type="text"
                  size="small"
                  @click="showDetailInfo(scope.row.code)"
                  >详情
                </el-button>
                <el-button
                  v-if="scope.row.type !== 10"
                  type="text"
                  size="small"
                  @click="updateInfo(scope.row.code)"
                >
                  编辑
                </el-button>
                <el-button
                  type="text"
                  size="small"
                  @click="confirmDelete(scope.row.code)"
                >
                  删除
                </el-button>
                <el-button
                  v-if="scope.row.type !== 10"
                  type="text"
                  size="small"
                  @click="confirmDowngradeInfo(scope.row.code)"
                >
                  降级
                </el-button>
                <el-button
                  v-if="scope.row.type === 10"
                  type="text"
                  size="small"
                  @click="upgradeInfo(scope.row.code)"
                >
                  升级
                </el-button>
                <el-button
                  type="text"
                  size="small"
                  @click="setTags(scope.row.code)"
                >
                  设置标签
                </el-button>
                <el-button
                  v-if="scope.row.status === 20"
                  type="text"
                  size="small"
                  @click="enableInfo(scope.row.code)"
                >
                  启用
                </el-button>
                <el-button
                  v-if="scope.row.status === 10"
                  type="text"
                  size="small"
                  @click="disableInfo(scope.row.code)"
                >
                  禁用
                </el-button>
              </template>
            </el-table-column>
          </table-list>
        </el-tab-pane>
      </el-tabs>
      <el-dialog
        title="新增/编辑合伙人"
        width="1000px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="infoVisible"
      >
        <el-form :model="info">
          <el-row :gutter="20">
            <el-col :span="6">
              <el-form-item label="合伙人类型">
                <el-select v-model="info.type" placeholder="请选择合伙人类型">
                  <el-option
                    v-for="(option, index) in infoTypeList"
                    :key="index"
                    :label="option.name"
                    :value="option.code"
                  ></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 30 || info.type === 40" :span="6">
              <el-form-item label="激活码">
                <el-input
                  v-model="info.activationCode"
                  placeholder="请输入激活码"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 30 || info.type === 40" :span="6">
              <el-form-item label="员工姓名">
                <el-input
                  v-model="info.inlineName"
                  placeholder="请输入员工姓名"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 30 || info.type === 40" :span="6">
              <el-form-item label="工卡编号/电话">
                <el-input
                  v-model="info.inlineNumber"
                  placeholder="请输入工卡编号/电话"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 30 || info.type === 40" :span="6">
              <el-form-item label="一级部门">
                <el-input
                  v-model="info.firstDepartment"
                  placeholder="请输入一级部门"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 30 || info.type === 40" :span="6">
              <el-form-item label="二级部门">
                <el-input
                  v-model="info.secondDepartment"
                  placeholder="请输入二级部门"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 30 || info.type === 40" :span="6">
              <el-form-item label="三级部门">
                <el-input
                  v-model="info.thirdDepartment"
                  placeholder="请输入三级部门"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20 || info.type === 50" :span="6">
              <el-form-item label="分公司">
                <el-input
                  v-model="info.companyName"
                  placeholder="请输入分公司"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20" :span="6">
              <el-form-item label="区域信息">
                <el-input
                  v-model="info.area"
                  placeholder="请输入区域信息"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50" :span="6">
              <el-form-item label="经办名称">
                <el-input
                  v-model="info.handingName"
                  placeholder="请输入经办名称"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50" :span="6">
              <el-form-item label="网点编号">
                <el-input
                  v-model="info.networkCode"
                  placeholder="请输入网点编号"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50" :span="6">
              <el-form-item label="网点名称">
                <el-input
                  v-model="info.networkName"
                  placeholder="请输入网点名称"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50" :span="6">
              <el-form-item label="上级客户">
                <el-input
                  v-model="info.parentClientName"
                  placeholder="请输入上级客户"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50" :span="6">
              <el-form-item label="上级客户编码">
                <el-input
                  v-model="info.parentClientCode"
                  placeholder="请输入上级客户编码"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20" :span="6">
              <el-form-item label="客户名称">
                <el-input
                  v-model="info.clientName"
                  placeholder="请输入客户名称"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20" :span="6">
              <el-form-item label="R3编码">
                <el-input
                  v-model="info.r3Code"
                  placeholder="请输入R3编码"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20" :span="6">
              <el-form-item label="合并编码">
                <el-input
                  v-model="info.mergeCode"
                  placeholder="请输入合并编码"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 20" :span="6">
              <el-form-item label="客户类型">
                <el-input
                  v-model="info.clientType"
                  placeholder="请输入客户类型"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50 || info.type === 20" :span="6">
              <el-form-item label="省份">
                <el-input
                  v-model="info.province"
                  placeholder="请输入客户地址省份"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50 || info.type === 20" :span="6">
              <el-form-item label="城市">
                <el-input
                  v-model="info.city"
                  placeholder="请输入客户地址城市"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50 || info.type === 20" :span="6">
              <el-form-item label="县区">
                <el-input
                  v-model="info.county"
                  placeholder="请输入客户地址县区"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50 || info.type === 20" :span="6">
              <el-form-item label="乡镇">
                <el-input
                  v-model="info.town"
                  placeholder="请输入客户地址乡镇"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50 || info.type === 20" :span="6">
              <el-form-item label="客户地址">
                <el-input
                  v-model="info.companyAddress"
                  placeholder="请输入客户地址"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50 || info.type === 20" :span="6">
              <el-form-item label="业务员">
                <el-input
                  v-model="info.salesman"
                  placeholder="请输入业务员"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col v-if="info.type === 50 || info.type === 20" :span="6">
              <el-form-item label="业务员电话">
                <el-input
                  v-model="info.salesmanPhone"
                  placeholder="请输入业务员电话"
                ></el-input>
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="infoVisible = false">
            取消
          </el-button>
          <el-button type="primary" @click="saveInfo">
            保存
          </el-button>
        </div>
      </el-dialog>
      <el-dialog
        title="设置标签"
        width="538px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="tagVisible"
      >
        <el-form>
          <el-transfer
            v-model="tags"
            filterable
            :titles="['未选择标签', '已选择标签']"
            filter-placeholder="请输入关键字"
            :data="tagList"
            :props="{
              key: 'code',
              label: 'name'
            }"
          ></el-transfer>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="tagVisible = false">
            取消
          </el-button>
          <el-button type="primary" @click="saveTags">
            保存
          </el-button>
        </div>
      </el-dialog>
      <el-dialog
        title="合伙人详情"
        width="1244px"
        :modal-append-to-body="false"
        :close-on-click-modal="false"
        :visible.sync="detail.visible"
      >
        <div class="detail-content">
          <div class="detail-box">
            <div class="detail-label">合伙人类型</div>
            <div class="detail-value">{{ detail.info.typeText }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">激活码</div>
            <div class="detail-value">{{ detail.info.activationCode }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">合伙人昵称</div>
            <div class="detail-value">{{ detail.info.nickname }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">合伙人编号</div>
            <div class="detail-value">{{ detail.info.code }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">真实姓名</div>
            <div class="detail-value">{{ detail.info.idName }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">身份证号码</div>
            <div class="detail-value">{{ detail.info.idNumber }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">联系电话</div>
            <div class="detail-value">{{ detail.info.clientPhone }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">员工姓名</div>
            <div class="detail-value">{{ detail.info.inlineName }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">工卡编号/电话</div>
            <div class="detail-value">{{ detail.info.inlineNumber }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">一级部门</div>
            <div class="detail-value">{{ detail.info.firstDepartment }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">二级部门</div>
            <div class="detail-value">{{ detail.info.secondDepartment }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">三级部门</div>
            <div class="detail-value">{{ detail.info.thirdDepartment }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">分公司</div>
            <div class="detail-value">{{ detail.info.companyName }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">R3编码</div>
            <div class="detail-value">{{ detail.info.r3Code }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">客户名称</div>
            <div class="detail-value">{{ detail.info.clientName }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">客户类型</div>
            <div class="detail-value">{{ detail.info.clientType }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">区域信息</div>
            <div class="detail-value">{{ detail.info.area }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">合并编码</div>
            <div class="detail-value">{{ detail.info.mergeCode }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">省</div>
            <div class="detail-value">{{ detail.info.province }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">市</div>
            <div class="detail-value">{{ detail.info.city }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">县</div>
            <div class="detail-value">{{ detail.info.county }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">乡/镇</div>
            <div class="detail-value">{{ detail.info.town }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">客户地址</div>
            <div class="detail-value">{{ detail.info.companyAddress }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">业务员姓名</div>
            <div class="detail-value">{{ detail.info.salesman }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">业务员电话</div>
            <div class="detail-value">{{ detail.info.salesmanPhone }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">经办名称</div>
            <div class="detail-value">{{ detail.info.handingName }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">网点编号</div>
            <div class="detail-value">{{ detail.info.networkCode }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">网点名称</div>
            <div class="detail-value">{{ detail.info.networkName }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">上级客户</div>
            <div class="detail-value">{{ detail.info.parentClientName }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">上级客户编码</div>
            <div class="detail-value">{{ detail.info.parentClientCode }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">上级合伙人名称</div>
            <div class="detail-value">{{ detail.info.parentName }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">上级合伙人编号</div>
            <div class="detail-value">{{ detail.info.parentCode }}</div>
          </div>
          <div class="detail-box">
            <div class="detail-label">行业/职业</div>
            <div class="detail-value">{{ detail.info.userCategory }}</div>
          </div>
        </div>
        <div slot="footer" class="dialog-footer">
          <el-button @click="detail.visible = false">
            关闭
          </el-button>
        </div>
      </el-dialog>
    </el-main>
  </el-container>
</template>

<script>
import TableList from '../../components/TableList'
import SearchBox from '../../components/SearchBox'
import ImportButton from '../../components/ImportButton'
import ExportButton from '../../components/ExportButton'

export default {
  components: { ExportButton, ImportButton, SearchBox, TableList },
  data() {
    return {
      inputSearchKeyword: this.defaultSearchKeyword(),
      tagVisible: false,
      tagCode: null,
      tags: [],
      tagList: [],
      searchKeywords: {},
      infoVisible: false,
      code: null,
      info: this.defaultInfo(),
      typeList: [],
      statusList: [],
      importRules: {
        B: 'type',
        C: 'activationCode',
        D: 'province',
        E: 'city',
        F: 'county',
        G: 'town',
        H: 'companyAddress',
        I: 'inlineName',
        J: 'inlineNumber',
        K: 'firstDepartment',
        L: 'secondDepartment',
        M: 'thirdDepartment',
        N: 'companyName',
        O: 'r3Code',
        P: 'clientName',
        Q: 'clientType',
        R: 'area',
        S: 'mergeCode',
        T: 'salesman',
        U: 'salesmanPhone',
        V: 'handingName',
        W: 'networkCode',
        X: 'networkName',
        Y: 'parentClientName',
        Z: 'parentClientCode'
      },
      tmpDown: '',
      detail: {
        info: {
          typeText: '',
          parentName: '',
          userCategory: '',
          activationCode: ''
        },
        visible: false
      },
      selectRows: []
    }
  },
  computed: {
    infoTypeList() {
      return this.typeList.filter(type => {
        return type.code !== 10
      })
    }
  },
  mounted() {
    this.loadTypeList()
    this.loadStatusList()
    this.loadTagList()
  },
  methods: {
    tableColumn(type) {
      const columnList = [
        {
          type: [0],
          label: '分公司/一级部门',
          prop: 'firstDepartment'
        },
        {
          type: [0],
          label: '员工姓名/客户名称',
          prop: 'firstDepartment'
        },
        {
          type: [30, 40],
          label: '员工姓名',
          prop: 'inlineName'
        },
        {
          type: [30, 40],
          label: '工卡编号/电话',
          prop: 'inlineNumber'
        },
        {
          type: [30, 40],
          label: '一级部门',
          prop: 'firstDepartment'
        },
        {
          type: [30, 40],
          label: '二级部门',
          prop: 'secondDepartment'
        },
        {
          type: [30, 40],
          label: '三级部门',
          prop: 'thirdDepartment'
        },
        {
          type: [20, 50],
          label: '分公司',
          prop: 'companyName'
        },
        {
          type: [20],
          label: 'R3编码',
          prop: 'r3Code'
        },
        {
          type: [20],
          label: '客户名称',
          prop: 'clientName'
        },
        {
          type: [20],
          label: '客户类型',
          prop: 'clientType'
        },
        {
          type: [20],
          label: '合并编码',
          prop: 'mergeCode'
        },
        {
          type: [50],
          label: '经办名称',
          prop: 'handingName'
        },
        {
          type: [50],
          label: '网点编号',
          prop: 'networkCode'
        },
        {
          type: [50],
          label: '网点名称',
          prop: 'networkName'
        },
        {
          type: [50],
          label: '上级客户',
          prop: 'parentClientName'
        },
        {
          type: [50],
          label: '上级客户编码',
          prop: 'parentClientCode'
        },
        {
          type: [20, 50],
          label: '客户地址',
          prop: 'companyAddress'
        },
        {
          type: [10],
          label: '上级合伙人昵称',
          prop: 'parentName'
        },
        {
          type: [10],
          label: '上级合伙人编码',
          prop: 'parentCode'
        },
        {
          type: [10],
          label: '行业/职业',
          prop: 'parentClientCode'
        }
      ]
      return columnList.filter(column => {
        return column.type.indexOf(type) !== -1
      })
    },
    defaultSearchKeyword() {
      return {
        type: '0',
        inviteCode: '',
        idName: '',
        idNumber: '',
        code: '',
        clientPhone: '',
        //
        inlineName: '',
        inlineNumber: '',
        firstDepartment: '',
        secondDepartment: '',
        thirdDepartment: '',
        //
        companyName: '',
        r3Code: '',
        clientName: '',
        clientType: '',
        area: '',
        mergeCode: '',
        companyAddress: '',
        salesman: '',
        salesmanPhone: '',
        //
        handingName: '',
        networkName: '',
        networkCode: '',
        parentClientCode: '',
        parentClientName: '',
        //
        province: '',
        city: '',
        county: '',
        town: '',
        status: ''
      }
    },
    defaultInfo() {
      return {
        type: 20,
        area: '',
        companyName: '',
        clientName: '',
        clientPhone: '',
        r3Code: '',
        mergeCode: '',
        clientType: '',
        companyAddress: '',
        salesman: '',
        salesmanPhone: '',
        parentClientCode: '',
        parentClientName: '',
        networkName: '',
        networkCode: '',
        handingName: '',
        thirdDepartment: '',
        inlineName: '',
        inlineNumber: '',
        firstDepartment: '',
        secondDepartment: ''
      }
    },
    createInfo() {
      this.info = this.defaultInfo()
      this.infoVisible = true
      this.code = null
    },
    updateInfo(code) {
      this.$axios
        .get('/admin/partners/' + code)
        .then(response => {
          this.info = response.data
          this.code = code
          this.infoVisible = true
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    enableInfo(code) {
      this.$axios
        .put('/admin/partners/' + code + '/enabled')
        .then(response => {
          this.$message.success(response.message)
          this.refreshTable()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    disableInfo(code) {
      this.$axios
        .put('/admin/partners/' + code + '/disabled')
        .then(response => {
          this.$message.success(response.message)
          this.refreshTable()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    confirmDelete(code) {
      this.$confirm('此操作将永久删除该记录, 是否继续?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.deleteInfo(code)
      })
    },
    deleteInfo(code) {
      this.$axios
        .delete('/admin/partners/' + code)
        .then(response => {
          this.$message.success(response.message)
          this.refreshTable()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    confirmDowngradeInfo(code) {
      this.$confirm('确定要把此合伙人降级为二类合伙人吗?', '警告', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.downgradeInfo(code)
      })
    },
    downgradeInfo(code) {
      this.$axios
        .put('/admin/partners/' + code + '/downgrade')
        .then(response => {
          this.$message.success(response.message)
          this.refreshTable()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    upgradeInfo(code) {
      this.$axios
        .get('/admin/partners/' + code)
        .then(response => {
          this.info = response.data
          this.info.type = 20
          this.code = code
          this.infoVisible = true
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    saveInfo() {
      this.$axios
        .request({
          method: this.code === null ? 'post' : 'put',
          url: '/admin/partners' + (this.code === null ? '' : '/' + this.code),
          data: this.info
        })
        .then(response => {
          this.$message.success(response.message)
          this.infoVisible = false
          this.refreshTable()
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    selectToRows(event) {
      this.selectRows = event.map(function(row) {
        return row.code
      })
    },
    switchType() {
      this.resetSearch()
      this.confirmSearch()
    },
    refreshTable() {
      const tableName = 'tableList' + this.searchKeywords.type
      const tableList =
        this.searchKeywords.type === '0'
          ? this.$refs.tableList
          : this.$refs[tableName][0]
      tableList.refresh()
    },
    confirmSearch() {
      this.searchKeywords = JSON.parse(JSON.stringify(this.inputSearchKeyword))
      const tableName = 'tableList' + this.searchKeywords.type
      const tableList =
        this.searchKeywords.type === '0'
          ? this.$refs.tableList
          : this.$refs[tableName][0]
      tableList.reload()
    },
    showDetailInfo(code) {
      this.$axios
        .get('/admin/partners/' + code)
        .then(response => {
          this.detail.info = response.data
          this.detail.visible = true
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    resetSearch() {
      const searchKeywords = JSON.parse(
        JSON.stringify(this.defaultSearchKeyword())
      )
      searchKeywords.type = this.inputSearchKeyword.type
      this.inputSearchKeyword = searchKeywords
    },
    loadTypeList() {
      this.$axios
        .get('/admin/select/partner-type')
        .then(response => {
          this.typeList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    setTags(code) {
      this.$axios
        .get('/admin/partners/' + code + '/tags')
        .then(response => {
          this.tagCode = code
          this.tags = response.data
          this.tagVisible = true
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    columnFormatter(rowData, column) {
      return rowData[column.prop]
    },
    saveTags() {
      this.$axios
        .put('/admin/partners/' + this.tagCode + '/tags', this.tags)
        .then(response => {
          this.tagVisible = false
          this.$message.success(response.message)
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadTagList() {
      this.$axios
        .get('/admin/select/tags/partner')
        .then(response => {
          this.tagList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    },
    loadStatusList() {
      this.$axios
        .get('/admin/select/partner-status')
        .then(response => {
          this.statusList = response.data
        })
        .catch(error => {
          this.$message.error(error.message)
        })
    }
  }
}
</script>

<style scoped>
.detail-content {
  overflow: hidden;
  border-top: 1px solid #bfcad4;
}

.detail-box {
  border-bottom: 1px solid #bfcad4;
  overflow: hidden;
  float: left;
  width: 400px;
}

.detail-box:first-child {
  border-left: 1px solid #bfcad4;
}

.detail-box:last-child {
  border-right: 1px solid #bfcad4;
}

.detail-box:nth-child(3n) {
  border-right: 1px solid #bfcad4;
}

.detail-box + .detail-box {
  border-left: 1px solid #bfcad4;
}

.detail-label {
  float: left;
  width: 150px;
  text-align: center;
  background: #f2f2f2;
  padding: 10px 20px;
  box-sizing: border-box;
  border-right: 1px solid #bfcad4;
}

.detail-value {
  float: left;
  width: calc(100% - 150px);
  text-align: left;
  padding: 10px 20px;
  box-sizing: border-box;
}
</style>
