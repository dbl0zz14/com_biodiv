<?php

namespace MammalWeb\Component\Biodiv\Api\View\Classify;


use Joomla\CMS\MVC\View\JsonApiView as BaseApiView;

class JsonapiView extends BaseApiView
{
    protected $fieldsToRenderItem = [
        'id',
    ];

    protected $fieldsToRenderList = [
        'id',
    ];
}

?>