<?php

namespace SiteOrigin\Imgix;

use Illuminate\Support\ServiceProvider;
use Imgix\UrlBuilder;

class ImgixServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(UrlBuilder::class, function () {
            $url = parse_url(config('filesystems.disks.imgix.url'));

            return new UrlBuilder($url['host'], $url['scheme']==='https', '', false);
        });

    }

    public function boot()
    {
    }
}