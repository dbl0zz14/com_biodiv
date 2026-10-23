<?php

namespace MammalWeb\Component\Biodiv\Api\Controller;



use Joomla\CMS\MVC\Controller\ApiController;

use MammalWeb\Component\Biodiv\Api\Controller\BiodivController;
use MammalWeb\Component\Biodiv\Api\Model\ClassifyModel;


class ClassifyController extends BiodivController
{
    protected $contentType = 'classify';

    protected $default_view = 'classify';
	
}

?>