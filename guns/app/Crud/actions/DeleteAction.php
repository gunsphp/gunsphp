<?php

class DeleteAction extends CrudAppController
{

    public function deleteData()
    {
        $modelName = $this->Request->post('modelName');
        $id = $this->Request->post('dataid');
        $model = $modelName::find($id);
        $model->delete();
        Cache::clean();
        alert("$modelName with the Id: $id has been deleted successfully!");
        redirect(Router::urlFromName('crud_read', array('modelName' => $modelName)), true);
    }

} 