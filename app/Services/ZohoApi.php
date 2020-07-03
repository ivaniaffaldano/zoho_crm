<?php

namespace App\Services;

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\setup\restclient\ZCRMOrganization;
use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\oauth\ZohoOAuth;
use zcrmsdk\crm\exception\ZCRMException;

class ZohoApi {

    protected $oAuthClient;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(){

        $configuration=array(
            "client_id"=>env('ZOHO_CLIENT_ID', false),
            "client_secret"=>env('ZOHO_CLIENT_SECRET', false),
            "redirect_uri"=>env('ZOHO_REDIRECT_URI', false),
            "currentUserEmail"=>env('ZOHO_CURRENT_USER_EMAIL', false),
            "applicationLogFilePath"=>'/var/www/storage/logs',
            "db_port"=>env('DB_PORT', false),
            "db_username"=>env('DB_USERNAME', false),
            "db_password"=>env('DB_PASSWORD', false),
            "token_persistence_path"=>'/var/www/storage/zoho',
            "accounts_url"=>env('ZOHO_ACCOUNTS_URL', false),
            "apiBaseUrl"=>env('ZOHO_API_BASE_URL', false),
            "apiVersion"=>"v2"
        );

        ZCRMRestClient::initialize($configuration);
        $this->refreshToken();
        return $this;
    }

    public function refreshToken(){
        $this->oAuthClient = ZohoOAuth::getClientInstance();
        $this->oAuthClient->generateAccessTokenFromRefreshToken(env('ZOHO_REFRESH_TOKEN', false),env('ZOHO_CURRENT_USER_EMAIL', false));
    }

    public function getAllFields($name){
        $rest = ZCRMRestClient::getInstance()->getModuleInstance($name); // To get module instance
        $response = $rest->getAllFields(); // to get the field
        $fields = $response->getData();
        return $fields;
    }

    public function getFieldsInfoToArray($fields){
        return array_map(function($field) {
            $lookup = array();
            if ($field->getDataType() == "Lookup") {
                $lookup["module"] = $lookupfield->getModule();
                $lookup["displayLabel"] = $lookupfield->getDisplayLabel();
                $lookup["id"] = $lookupfield->getId();
            }
            $pickLists = array();
            $picklistfieldvalues = $field->getPickListFieldValues(); // to get the pick list values of the field
            foreach ($picklistfieldvalues as $picklistfieldvalue) {
                $pickList = array();
                $pickList["displayValue"] = $picklistfieldvalue->getDisplayValue();
                $pickList["sequenceNumber"] = $picklistfieldvalue->getSequenceNumber();
                $pickList["actualValue"] = $picklistfieldvalue->getActualValue();
                $pickList["maps"] = $picklistfieldvalue->getMaps();
                $pickLists[] = $pickList;
            }
            return array(
                "id" =>             $field->getId(),
                "apiName" =>        $field->getApiName(),
                "length" =>         $field->getLength(),
                "visible" =>        $field->isVisible(),
                "label" =>          $field->getFieldLabel(),
                "isMandatory" =>    $field->isMandatory(),
                "type" =>           $field->getDataType(),
                "defaultValue" =>   $field->getDefaultValue(),
                "lookup" =>         $lookup,
                "pickList" =>       $pickLists
            );
        }, $fields);
    }

    public function getEntity($entityType,$id){
        if(strpos($id,'@')===FALSE){
            return $this->getEntityById($entityType,$id);
        }else{
            return $this->getEntityByEmail($entityType,$id);
        }
    }

    public function getEntityById($entityType,$id){
        $moduleIns = ZCRMRestClient::getInstance()->getModuleInstance($entityType);
        try{
            $response = $moduleIns->getRecord($id);
        }catch(ZCRMException $e){
            return array("error"=>$e->getMessage());
        }
        $record = $response->getData();
        $responseRecords = array();
        $responseRecords[] = $this->parseSingleRecord($record);
        return $responseRecords;
    }

    public function getEntityByEmail($entityType,$email){
        // return max 200 entities searching by email
        $moduleIns = ZCRMRestClient::getInstance()->getModuleInstance($entityType);
        $param_map=array("page"=>1,"per_page"=>200);
        try{
            $response = $moduleIns->searchRecordsByEmail($email,$param_map);
        }catch(ZCRMException $e){
            return array("error"=>$e->getMessage());
        }
        $records = $response->getData();
        $responseRecords = array();
        foreach ($records as $record) {
            $responseRecords[] = $this->parseSingleRecord($record);
        }
        return $responseRecords;
    }

    public function parseSingleRecord($record){
        $returnRecord = array();
        $returnRecord["id"] = $record->getEntityId();
        $returnRecord["moduleName"] = $record->getModuleApiName();
        $owner = $record->getOwner();
        $map = $record->getData();
        $returnRecord["fields"] = array();
        foreach ($map as $key => $value) {
            $returnRecord["fields"][$key] = $value;
        }
        $returnRecord["fields"]["Owner"] = $owner->getId();
        return $returnRecord;
    }

    public function createEntity($entityType,$request){
        $moduleIns=ZCRMRestClient::getInstance()->getModuleInstance($entityType);
        $records=array();
        $record=ZCRMRecord::getInstance($entityType,null);
        $data = $request->except('_token','_entityType');
        foreach($data as $key => $value){
            $record->setFieldValue($key,$value);
        }
        $records[] = $record;
        $responseIn = $moduleIns->createRecords($records,array());
        foreach ($responseIn->getEntityResponses() as $responseIns) {
            if($responseIns->getStatus() == "error"){
                return array("error"=>$responseIns->getMessage(),"details"=>$responseIns->getDetails());
            }else{
                return array("success"=>$responseIns->getMessage(),"details"=>$responseIns->getDetails());
            }
        }
    }

    public function deleteEntity($entityType,$id){
        $moduleIns = ZCRMRestClient::getInstance()->getModuleInstance($entityType); // to get the instance of the module
        $recordids = array($id);
        $responseIn = $moduleIns->deleteRecords($recordids);
        foreach ($responseIn->getEntityResponses() as $responseIns) {
            if($responseIns->getStatus() == "error"){
                return array("error"=>$responseIns->getMessage(),"details"=>$responseIns->getDetails());
            }else{
                return array("success"=>$responseIns->getMessage(),"details"=>$responseIns->getDetails());
            }
        }
    }

    public function updateEntity($entityType,$id,$request){
        $moduleIns=ZCRMRestClient::getInstance()->getModuleInstance($entityType);
        $records=array();
        $record=ZCRMRecord::getInstance($entityType,$id);
        $data = $request->except('_token','_entityType','_id');
        foreach($data as $key => $value){
            $record->setFieldValue($key,$value);
        }
        $records[] = $record;
        $responseIn = $moduleIns->updateRecords($records,array());
        foreach ($responseIn->getEntityResponses() as $responseIns) {
            if($responseIns->getStatus() == "error"){
                return array("error"=>$responseIns->getMessage(),"details"=>$responseIns->getDetails());
            }else{
                return array("success"=>$responseIns->getMessage(),"details"=>$responseIns->getDetails());
            }
        }
    }

}
