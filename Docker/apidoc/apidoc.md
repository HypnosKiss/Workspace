## 使用yii-apidoc生成文档
##  "phpdocumentor/reflection-docblock" : "^2.0.4",
##  "cebe/markdown" : "~1.0.0 | ~1.1.0",
##  "yiisoft/yii2-apidoc": "~2.1.0"
## composer update yiisoft/yii2-apidoc cebe/markdown phpdocumentor/reflection-docblock

vendor/bin/apidoc api ./api/modules/v1/controllers ./doc
## ./api/modules/v1/controllers 操作目录(API目录)
## ./doc 输出目录