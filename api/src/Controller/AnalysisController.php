<?php

namespace MammalWeb\Component\Biodiv\Api\Controller;



use Joomla\CMS\MVC\Controller\ApiController;

use MammalWeb\Component\Biodiv\Api\Controller\BiodivController;
use MammalWeb\Component\Biodiv\Api\Model\AnalysisModel;


class AnalysisController extends BiodivController
{
    protected $contentType = 'analysis';

    protected $default_view = 'analysis';
	
	public function displayItem($id = null)
    {
        if ($id === null) {
            $id = $this->input->get('id', 0, 'int');
        }

        $viewType   = $this->app->getDocument()->getType();
        $viewName   = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');

        try {
            /** @var JsonApiView $view */
            $view = $this->getView(
                $viewName,
                $viewType,
                '',
                ['base_path' => $this->basePath, 'layout' => $viewLayout, 'contentType' => $this->contentType]
            );
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

		// Singularize removes the s from analysis so miss out
        $modelName = $this->input->get('model', $this->contentType);

        // Create the model, ignoring request data so we can safely set the state in the request from the controller
        $model = $this->getModel($modelName, '', ['ignore_request' => true, 'state' => $this->modelState]);

        if (!$model) {
            throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_MODEL_CREATE'));
        }

        try {
            $modelName = $model->getName();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $model->setState($modelName . '.id', $id);

        // Push the model into the view (as default)
         $view->setModel($model, true);

        $view->document = $this->app->getDocument();
        $view->displayItem();

        return $this;
    }
}

?>