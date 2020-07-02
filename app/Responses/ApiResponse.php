<?php

namespace App\Responses;

class ApiResponse {

    protected $statusCode = 200;

    function __construct()
    {

    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function respondCreated($data, $message = 'Created')
    {
        return $this->setStatusCode(201)->respond([
            'type' => 'success',
            'message' => $message,
            'status_code' => $this->getStatusCode(),
            'data' => $data,
        ]);
    }

    public function respondUpdated($data, $message = 'Updated')
    {
        return $this->setStatusCode(200)->respond([
            'type' => 'success',
            'message' => $message,
            'status_code' => $this->getStatusCode(),
            'data' => $data,
        ]);
    }

    public function respondDeleted($data, $message = 'Deleted')
    {
        return $this->setStatusCode(200)->respond([
            'type' => 'success',
            'message' => $message,
            'status_code' => $this->getStatusCode(),
            'data' => $data,
        ]);
    }

    public function respondRestored($data, $message = 'Restored')
    {
        return $this->setStatusCode(200)->respond([
            'type' => 'success',
            'message' => $message,
            'status_code' => $this->getStatusCode(),
            'data' => $data,
        ]);
    }

    public function respondExists($message = 'The resource already exists')
    {
        return $this->setStatusCode(200)->respondWithMessage($message);
    }

    public function respondNotFound($message = 'Not found')
    {
        return $this->setStatusCode(204)->respondWithData([], $message);
    }

    public function respondInvalidData($message = 'Dati non validi')
    {
        return $this->setStatusCode(422)->respondWithMessage($message);
    }

    public function respondUnauthorized($message = 'Not authorized')
    {
        return $this->setStatusCode(403)->respondWithMessage($message);
    }

    public function respondInvalidQuery($exception)
    {
        return $this->setStatusCode(400)
                ->respondWithQuery(
                    $exception->getSql(),
                    $exception->getBindings(),
                    $exception->getMessage()
                );
    }

    public function respondInternalError($message = 'Internal error')
    {
        return $this->setStatusCode(500)->respondWithMessage($message);
    }

    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function respondWithMessage($message)
    {
        $type = $this->statusOk() ? 'success' : 'error';
        $response = [
            'type' => $type,
            'message' => $message,
            'status_code' => $this->getStatusCode()
        ];

        return $this->respond($response, ['message' => $message]);
    }

    public function respondWithData($data, $message = 'Content found')
    {
        $type = $this->statusOk() ? 'success' : 'error';
        $response = [
            'type' => $type,
            'data' => $data,
            'message' => $message,
            'status_code' => $this->getStatusCode()
        ];

        return $this->respond($response, ['message' => $message]);
    }

    public function respondWithQuery($query, $bindings, $message)
    {
        return $this->respond([
            'response' => [
                'type' => 'error',
                'message' => 'Errore query: ' . $message,
                'query' => $query,
                'bindings' => $bindings,
                'status_code' => $this->getStatusCode()
            ]
        ]);
    }

    /**
     * Controlla se lo status code attuale è nel gruppo dei 200
     *
     * @return boolean
     **/
    private function statusOk()
    {
        return in_array($this->getStatusCode(), [200, 201]);
    }
}
