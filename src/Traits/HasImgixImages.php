<?php

namespace SiteOrigin\Imgix\Traits;

use Illuminate\Support\Facades\Storage;
use SiteOrigin\Imgix\UrlBuilderWrapper;

trait HasImgixImages
{
    private array $builders = [];

    public function imgix(string $attribute): UrlBuilderWrapper
    {
        if (!empty($this->builders[$attribute])) return $this->builders[$attribute];

        $url = Storage::disk('imgix')->url($this->getAttribute($attribute));
        return $this->builders[$attribute] = new UrlBuilderWrapper($url);
    }

}