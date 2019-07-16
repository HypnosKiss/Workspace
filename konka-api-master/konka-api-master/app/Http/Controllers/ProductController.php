<?php

namespace App\Http\Controllers;


use App\Extend\PosterBuildService;
use app\Extend\WeChatService;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\ProductTag;
use App\Models\SearchRecord;
use App\UserEvents\Product\BatchPoints;
use App\UserEvents\Product\CreateProduct;
use App\UserEvents\Product\DeleteProduct;
use App\UserEvents\Product\UpdateProduct;
use App\UserEvents\Product\UpdateTags;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Exceptions\CustomException;
use ZhiEq\Utils\ListQueryBuilder;
use ZhiEq\Utils\SearchKeyword;
use ZhiEq\Utils\Trigger;

class ProductController extends Controller
{
    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getList()
    {
        return success(ListQueryBuilder::create(Product::query())
            ->withSearch([
                [
                    'key' => 'status',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ]
            ])->withAppends([
                'product_category_name', 'status_text', 'is_hot_text', 'is_recommend_text', 'is_new_text', 'main_image_url', 'create_person_name'
            ])->withHidden(['specification_array'])->withPage()->paginateList());
    }

    /**
     * 上架产品列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEnableList(Request $request)
    {
        $searchKeywords = $request->header('X-Search-Keywords', null);
        $searchKeywords = !empty($searchKeywords) ? json_decode(base64_decode($searchKeywords), true) : [];
        $model = Product::enable();
        if (!empty($searchKeywords['title'])) {
            $model->where(function (Builder $query) use ($searchKeywords) {
                $query->orWhere('sub_title', 'like', '%' . $searchKeywords['title'] . '%')->orWhere('title', 'like', '%' . $searchKeywords['title'] . '%');
            });
        }
        return success(ListQueryBuilder::create($model->with(['productCategory', 'productImage',
            'productSpecification']))->withSearch([
            [
                'key' => 'product_category_code',
                'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
            ]
        ])->withPage()->withAppends([
            'product_category_name', 'main_image_url'
        ])->withHidden(array_merge(['specification_array'], $this->listHidden()))
            ->withOrder('sales', ListQueryBuilder::ORDER_TYPE_DESC, ['sales'])
            ->paginateList());
    }

    /**
     * 搜索产品
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchEnableList(Request $request)
    {
        $searchKeywords = $request->header('X-Search-Keywords', null);
        $searchKeywords = !empty($searchKeywords) ? json_decode(base64_decode($searchKeywords), true) : [];
        $model = Product::enable()->with(['productCategory', 'productImage',
            'productSpecification']);
        if (empty($searchKeywords['title'])) {
            return errors('搜索内容不能为空');
        } else {
            $model->where(function (Builder $query) use ($searchKeywords) {
                $query->orWhere('sub_title', 'like', '%' . $searchKeywords['title'] . '%')->orWhere('title', 'like', '%' . $searchKeywords['title'] . '%');
            });
        }
        if (!empty(auth_user())) {
            (new SearchRecord())->setAttribute('content', $searchKeywords['title'])->setAttribute('user_code', auth_user()->code)->save();
        }
        return success(ListQueryBuilder::create($model)->withPage()->withAppends(
            [
                'product_category_name', 'main_image_url'
            ])->withHidden(array_merge(['specification_array'], $this->listHidden()))
            ->withOrder('sales', ListQueryBuilder::ORDER_TYPE_DESC, ['sales', 'price'])
            ->paginateList());
    }

    /**
     * 热销产品列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHotList()
    {
        return success(ListQueryBuilder::create(Product::whereIsHot(Product::IS_HOT_YES)->with(['productCategory', 'productImage',
            'productSpecification']))->withPage()->withAppends([
            'product_category_name', 'main_image_url', 'commission_amount'
        ])->withHidden(array_merge(['specification_array'], $this->listHidden()))->paginateList());
    }

    /**
     * 推荐产品列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecommendList()
    {
        return success(ListQueryBuilder::create(Product::whereIsRecommend(Product::IS_RECOMMEND_YES)->with(['productCategory', 'productImage',
            'productSpecification']))->withPage(20)->withAppends([
            'product_category_name', 'main_image_url', 'commission_amount'
        ])->withHidden(array_merge(['specification_array'], $this->listHidden()))->paginateList());
    }

    protected function listHidden()
    {
        return [
            'id', 'konka_product_code', 'min', 'max', 'per', 'start_at', 'end_at', 'create_person_code',
            'created_at', 'updated_at'
        ];
    }

    /**
     * 新产品列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNewList()
    {
        $info = ListQueryBuilder::create(Product::whereIsNew(Product::IS_NEW_YES)->with(['productCategory', 'productImage',
            'productSpecification']))->withAppends([
            'product_category_name', 'main_image_url', 'commission_amount'
        ])->withHidden(array_merge(['specification_array'], $this->listHidden()))->withPage()->paginateList();
        return success($info);
    }

    /**
     * 新增
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function postInfo(Request $request)
    {
        if (!Trigger::eventWithTransaction(new CreateProduct($request->input()))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 详情
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getInfo($code)
    {
        if (!$info = Product::whereCode($code)->first()) {
            throw new CustomException('产品编码不存在');
        }
        return success($info->append(['product_category_name', 'status_text', 'is_hot_text',
            'is_recommend_text', 'image', 'evaluation_num', 'one_evaluation', 'is_new_text', 'images',
            'commission_amount', 'share_code_url', 'share_poster_url',
            'content', 'product_specifications', 'main_image', 'main_image_url', 'specification']));
    }

    /**
     * 修改
     *
     * @param Request $request
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putInfo(Request $request, $code)
    {
        if (!Trigger::eventWithTransaction(new UpdateProduct($request->input(), $code))) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteInfo($code)
    {
        if (!Trigger::eventWithTransaction(new DeleteProduct($code))) {
            return errors('删除失败');
        }
        return success([], '删除成功');
    }

    /**
     * 批量修改产品佣金点数
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function batchPoints(Request $request)
    {
        if (!Trigger::eventWithTransaction(new BatchPoints($request->input()))) {
            return errors('保存失败');
        }
        return success();
    }

    /**
     * 启用
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putEnable($code)
    {
        if (!$info = Product::disable()->whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->setAttribute('status', Product::STATUS_ENABLE)->save()) {
            return errors('保存失败');
        }
        return success([], '上架成功');
    }

    /**
     * 禁用
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function putDisable($code)
    {
        if (!$info = Product::enable()->whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->setAttribute('status', Product::STATUS_DISABLE)->save()) {
            return errors('保存失败');
        }
        return success([], '下架成功');
    }

    /**
     * 生成产品详情小程序码
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */

    public function generateAppletsCode($code)
    {
        return success([
            'base64Image' => WeChatService::generateCode($code, 'pages/bargain-product-detail/main')
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getRecommendDefinition()
    {
        return success(definition_to_select(Product::getIsRecommendLabels()));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getHotDefinition()
    {
        return success(definition_to_select(Product::getIsHotLabels()));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getNewDefinition()
    {
        return success(definition_to_select(Product::getIsNewLabels()));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getRecycleList()
    {
        return success(ListQueryBuilder::create(Product::onlyTrashed())
            ->withSearch([
                [
                    'key' => 'status',
                    'type' => SearchKeyword::SEARCH_KEYWORD_TYPE_MATCH
                ]
            ])->withAppends([
                'product_category_name', 'status_text', 'is_hot_text', 'is_recommend_text', 'is_new_text', 'main_image_url', 'create_person_name'
            ])->withHidden(['specification_array'])->withPage()->paginateList());
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function restoreInfo($code)
    {
        if (!$info = Product::onlyTrashed()->whereCode($code)->first()) {
            return errors('编码不存在');
        }
        if (!$info->restore()) {
            return errors('保存失败');
        }
        return success([], '恢复成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSpecificationCombination($code)
    {
        if (!$info = Product::whereCode($code)->first()) {
            return errors('编码不存在');
        }
        return success(ListQueryBuilder::create(ProductSpecification::whereProductCode($info->code))
            ->withAppends(['specification_codes_text', 'image_url'])
            ->withPage()
            ->paginateList());
    }


    /**
     * @param $code
     * @param $specificationCode
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSpecificationCombinationInfo($code, $specificationCode)
    {
        if (!$product = Product::whereCode($code)->first()) {
            return errors('产品不存在');
        }
        if (!$specification = ProductSpecification::whereCode($specificationCode)->first()) {
            return errors('组合不存在');
        }
        if ($specification->product_code !== $product->code) {
            return errors('组合所属产品与产品编码不符');
        }
        return success($specification);
    }

    /**
     * @param $code
     * @param $specificationCode
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function putSpecificationCombinationInfo($code, $specificationCode, Request $request)
    {
        if (!$product = Product::whereCode($code)->first()) {
            return errors('产品不存在');
        }
        if (!$specification = ProductSpecification::whereCode($specificationCode)->first()) {
            return errors('组合不存在');
        }
        if ($specification->product_code !== $product->code) {
            return errors('组合所属产品与产品编码不符');
        }
        $rules = [
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['required'],
            'guidePrice' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0']
        ];
        $messages = [
            'price.required' => '销售价不能为空',
            'price.numeric' => '销售价必须为数值',
            'price.min' => '销售价必须大于等于0',
            'guidePrice.required' => '指导价不能为空',
            'guidePrice.numeric' => '指导价必须为数值',
            'guidePrice.min' => '指导价必须大于等于0',
            'image.required' => '规格图片不能为空',
            'stock.required' => '库存不能为空',
            'stock.integer' => '库存必须是整数',
            'stock.min' => '库存必须大于等于0'
        ];
        $this->validate($request, $rules, $messages);
        $fillKeys = array_keys($rules);
        if (!$specification->fillable(snake_case_array($fillKeys))
            ->fill(snake_case_array_keys($request->only($fillKeys)))
            ->save()) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTags($code)
    {
        if (!$product = Product::whereCode($code)->first()) {
            return errors('产品不存在');
        }
        return success(ProductTag::whereProductCode($code)->get()->pluck('tag_code'));
    }

    /**
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function putTags($code, Request $request)
    {
        if (!Trigger::eventWithTransaction(new UpdateTags($code, $request->input()))) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }

    /**
     * @param $code
     * @return array|\Illuminate\Http\JsonResponse
     */

    public function getProductPoster($code)
    {
        if (!$product = Product::whereCode($code)->first()) {
            return errors('产品不存在');
        }
        if (empty(auth_user()->partner)) {
            return errors('此账号不是合伙人');
        }
        return success(PosterBuildService::generatePoster($product));
    }
}
