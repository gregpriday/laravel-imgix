<?php

namespace SiteOrigin\Imgix\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use SiteOrigin\Imgix\UrlBuilderWrapper;

trait HasImgixImages
{
    private array $builders = [];

    public function imgix(string $attribute): UrlBuilderWrapper
    {
        if (!empty($this->builders[$attribute])) return $this->builders[$attribute];

        $url = Storage::disk( self::$imgixDisk ?? 'imgix' )->url($this->getAttribute($attribute));
        return $this->builders[$attribute] = new UrlBuilderWrapper($url);
    }

    public function imgixImageUrl(string $attribute, $params = [])
    {
        $defaultParams = self::$imgixDefaultParams ?? [];
        $params = array_merge($defaultParams, $params);
        return $this->imgix($attribute)->createURL($params);
    }

    /**
     * Calculates the primary color of an image by looking at a resized Imgix version.
     *
     * @param string $attribute
     * @return string
     */
    public function imgixImageColor(string $attribute)
    {
        $src = $this->imgix($attribute)->createURL([
            'w' => 10,
            'h' => 10,
            'fm' => 'jpg',
            'blur' => 200
        ]);

        return Cache::rememberForever('imgix_image_color:' . $src, function() use ($src){
            $im = imagecreatefromstring(file_get_contents($src));
            $palette = Palette::fromGD($im);
            $extractor = new ColorExtractor($palette);
            return Color::fromIntToHex($extractor->extract(1)[0]);
        });
    }

    public function imgixImageHtml($attribute, $params, $withColor=true): string
    {
        $defaultParams = self::$imgixDefaultParams ?? [];
        $params = array_merge($defaultParams, $params);

        $url = $this->imgix($attribute)->createURL($params);
        $color = $this->imgixImageColor($attribute);

        return '<img src="'.$url.'" width="900" height="300" style="background-color:' . $color . '" />';
    }

}