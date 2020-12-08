<?php

namespace SiteOrigin\Imgix;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imgix\UrlBuilder;

class UrlBuilderWrapper
{
    /**
     * @var string The path of this URL builder
     */
    protected $path;

    /**
     * @var \Imgix\UrlBuilder
     */
    private UrlBuilder $builder;

    public function __construct($url)
    {
        $url = parse_url($url);
        $this->path = $url['path'];
        $this->builder = new UrlBuilder($url['host'], $url['scheme']==='https', '', false);
    }

    public function __call($name, $arguments)
    {
        switch($name) {
            case 'createURL':
            case 'createSrcSet':
                return $this->builder->{$name}($this->path, ...$arguments);
                break;
            default :
                return $this->builder->{$name}(...$arguments);
                break;

        }
    }
}