<?php

namespace MammalWeb\Component\Biodiv\Api\Controller;

use Joomla\CMS\MVC\Controller\ApiController;

class BiodivController extends ApiController
{
    protected $contentType = 'biodiv';

    protected $default_view = 'biodiv';
	
	
	public function add() {
        // Access check.
        // if (!$this->allowAdd()) {
            // throw new NotAllowed('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED', 403);
        // }

        $recordId = $this->save();

        $this->displayItem($recordId);
    }
	
	protected function save($recordKey = null)
    {
		$model = $this->getModel($this->contentType);
		
		if (!$model) {
            throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_MODEL_CREATE'));
        }
		
		$data = $this->input->get('data', json_decode($this->input->json->getRaw(), true), 'array');
		//$input->json->getArray();
        
		$recordId = $model->saveItem ( $data );
		
		return $recordId;
	}
	
}

?>