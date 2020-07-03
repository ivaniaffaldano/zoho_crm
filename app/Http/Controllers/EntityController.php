<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Responses\ApiResponse;
use App\Services\ZohoApi;
use Illuminate\Http\Request;


class EntityController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function __construct(ApiResponse $response)
	{
        $this->response = $response;
    }

    public function getFields(Request $request){
        $entityType = $request->input('entity');
        $zohoCRM = new ZohoApi();
        $fields = $zohoCRM->getAllFields($entityType);
        return $this->response->respondWithData($zohoCRM->getFieldsInfoToArray($fields));
    }

    public function getEntity(Request $request){
        $entityType = $request->input('entity');
        $id = $request->input('id');
        $zohoCRM = new ZohoApi();
        $entity = $zohoCRM->getEntity($entityType,$id);
        if(isset($entity["error"])){ $this->response->setStatusCode(500); }
        return $this->response->respondWithData($entity);
    }

    public function createEntity(Request $request){
        $entityType = $request->input('_entityType');
        $zohoCRM = new ZohoApi();
        $response = $zohoCRM->createEntity($entityType,$request);
        if(isset($response["error"])){ $this->response->setStatusCode(500); }
        return $this->response->respondWithData($response);
    }

    public function deleteEntity(Request $request){
        $entityType = $request->input('entity');
        $id = $request->input('id');
        $zohoCRM = new ZohoApi();
        $entity = $zohoCRM->deleteEntity($entityType,$id);
        return $this->response->respondWithData($entity);
    }

    public function createEntityForm(Request $request){
        $entityType = $request->input('entity');
        $zohoCRM = new ZohoApi();
        $fields = $zohoCRM->getAllFields($entityType);
        return view("zoho/create_entity_form")->with("fields", $zohoCRM->getFieldsInfoToArray($fields))->with('entityType', $entityType);
    }

    public function updateEntityForm(Request $request){
        $entityType = $request->input('entity');
        $id = $request->input('id');
        $zohoCRM = new ZohoApi();
        $fields = $zohoCRM->getAllFields($entityType);
        $entity = $zohoCRM->getEntity($entityType,$id);
        if(isset($entity["error"])){ $this->response->setStatusCode(500); return $this->response->respondWithData($entity);}
        return view("zoho/update_entity_form")
                    ->with("fields", $zohoCRM->getFieldsInfoToArray($fields))
                    ->with("entity", $entity)
                    ->with('entityType', $entityType)
                    ->with('id', $id);
    }

    public function updateEntity(Request $request){
        $entityType = $request->input('_entityType');
        $id = $request->input('_id');
        $zohoCRM = new ZohoApi();
        $response = $zohoCRM->updateEntity($entityType,$id,$request);
        if(isset($response["error"])){ $this->response->setStatusCode(500); }
        return $this->response->respondWithData($response);
    }
}
