<?php

namespace App\Extend;


use App\Models\Product;
use Aws\S3\S3Client;
use Image;
use Intervention\Image\AbstractFont;
use Intervention\Image\Gd\Shapes\LineShape;

class PosterBuildService
{
    /**
     * @param Product $product
     * @return array
     */

    public static function generatePoster(Product $product)
    {
        return \Cache::tags(['posterImage'])->rememberForever(sha1(json_encode(['code' => $product->code, 'partner' => auth_user()->partner_code, 'mainUrl' => $product->main_image_url])), function () use ($product) {
            $imageBaseFilePath = resource_path('share-poster.jpg');
            $image = Image::make($imageBaseFilePath);
            $image->insert(Image::make($product->main_image_url)->resize(479, 479), 'top', 0, 124);
            $image->insert(Image::make($product->share_code_url)->resize(170, 170), 'bottom-left', 305, 145);
            $image->text(self::convertText(self::hiddenText($product->title, 34), 20), 155, 615, function (AbstractFont $font) {
                $font->file(base_path('resources/title-font.ttf'));
                $font->size(26);
                $font->valign('top');
                $font->color('#565656');
            });
            $image->text('￥'.$product->price, 228, 720, function (AbstractFont $font) {
                $font->file(base_path('resources/price-font.ttf'));
                $font->size(30);
                $font->valign('top');
                $font->color('#e7141a');
            });
            $image->text('￥'.$product->guide_price, 403, 725, function (AbstractFont $font) {
                $font->file(base_path('resources/price-font.ttf'));
                $font->size(24);
                $font->valign('top');
                $font->color('#cdcdcd');
            });
            $image->line(400, 730, 528, 730, function (LineShape $line) {
                $line->color('#cdcdcd');
            });
            /**
             * @var S3Client $s3
             */
            $s3 = app('minio');
            $fileName = sha1(microtime(true)) . '.jpg';
            $s3->putObject([
                'Bucket' => config('filesystems.disks.minio.bucket'),
                'Key' => $fileName,
                'Body' => $image->response('jpg')->getContent()
            ]);
            return ['fileName' => $fileName, 'fileUrl' => upload_file_to_url($fileName)];
        });
    }

    /**
     * @param $text
     * @param $num
     * @return string
     */

    protected static function hiddenText($text, $num)
    {
        $strLength = mb_strlen($text);
        if ($strLength <= $num) {
            return $text;
        }
        return mb_substr($text, 0, $num) . '...';
    }

    /**
     * @param $text
     * @param $split
     * @return string
     */

    protected static function convertText($text, $split)
    {
        $strLength = mb_strlen($text);
        if ($strLength <= $split) {
            return $text;
        }
        $returnString = '';
        $lineNumber = ceil($strLength / $split);
        for ($i = 0; $i < $lineNumber; $i++) {
            $returnString .= mb_substr($text, $i * $split, $split) . "\n";
        }
        return $returnString;
    }
}
