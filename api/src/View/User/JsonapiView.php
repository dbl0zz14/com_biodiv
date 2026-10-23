<?php

namespace MammalWeb\Component\Biodiv\Api\View\User;


use Joomla\CMS\MVC\View\JsonApiView as BaseApiView;

class JsonapiView extends BaseApiView
{
    protected $fieldsToRenderItem = [
        'id',
		'username',
		'email',
		'newuser',
        'expertise',
    ];

    protected $fieldsToRenderList = [
        'id',
    ];
}

?>