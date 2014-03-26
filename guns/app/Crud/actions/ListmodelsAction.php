<?php

class ListmodelsAction extends CrudAppController
{
    public function main()
    {
        $availableModels = Model::availableModels();
        $this->set('models', $availableModels);
    }
} 