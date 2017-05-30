<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class ApiController extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /**
     * Status Code
     */
    protected $statusCode = 200;

    /**
     * Get status code
     */
    public function getStatusCode()
    {
    	return $this->statusCode;
    }

    /**
     * Set Status code
     * @var int $statusCode
     */
    public function setStatusCode($statusCode)
    {
    	$this->statusCode = $statusCode;
    	return $this;
    }

    /**
     * Response error message
     * @param  string $message
     * @return mixed
     */
    public function respondWithError($message)
    {
        return response()->json([
            'errors' => [
                'message'   => $message,
                'code'      => $this->getStatusCode(),
            ]
        ], $this->getStatusCode());
    }

    /**
     * Response success message
     * @param  string $message
     * @return mixed
     */
    public function respondWithSuccess($message)
    {
        return response()->json([
            'success' => [
                'message'   => $message,
                'code'      => $this->getStatusCode(),
            ]
        ], $this->getStatusCode());
    }

    /**
     * Response validation error message
     * @param  string $validationData
     * @return mixed
     */
    public function respondValidationError($validationData)
    {
        return response()->json([
            'errors' => [
                'message'       => "Unprocessable Entity!",
                'code'          => 422,
            ],
            'validation'    => $validationData
        ], 422);
    }

    /**
     * Response success message with data
     * @param  string $data
     * @return mixed
     */
    public function respondSuccessWithData($data, $message = "OK!")
    {
        return response()->json([
            'success' => [
                'message'       =>  $message,
                'code'          => 200
            ],
            'data'    => $data
        ], 200);
    }

    /**
     * OK
     * @param  string $msssage
     * @return mixed
     */
    public function respondOK($msssage = 'OK!')
    {
        return $this->respondWithSuccess($msssage);
    }

    /**
     * Created
     * @param  string $msssage
     * @return mixed
     */
    public function respondCreated($msssage = 'Created!')
    {
        return $this->setStatusCode(201)->respondWithSuccess($msssage);
    }

    /**
     * Created
     * @param  string $message
     * @return mixed
     */
    public function respondCreatedWithData($data, $message = 'Created!')
    {
        return response()->json([
            'success' => [
                'message'       =>  $message,
                'code'          => 201
            ],
            'data'    => $data
        ], 201);
    }

    /**
     * Accepted
     * @param  string $msssage
     * @return mixed
     */
    public function respondAccepted($msssage = 'Accepted!')
    {
        return $this->setStatusCode(202)->respondWithSuccess($msssage);
    }

    /**
     * Bad Request
     * @param  string $msssage
     * @return mixed
     */
    public function respondBadRequest($msssage = 'Bad Request!')
    {
        return $this->setStatusCode(400)->respondWithError($msssage);
    }

    /**
     * Unauthorized
     * @param  string $msssage
     * @return mixed
     */
    public function respondUnauthorized($msssage = 'Unauthorized!')
    {
        return $this->setStatusCode(401)->respondWithError($msssage);
    }

    /**
     * Payment Required
     * @param  string $msssage
     * @return mixed
     */
    public function respondPaymentRequired($msssage = 'Payment Required!')
    {
        return $this->setStatusCode(402)->respondWithError($msssage);
    }

    /**
     * Forbidden
     * @param  string $msssage
     * @return mixed
     */
    public function respondForbidden($msssage = 'Forbidden!')
    {
        return $this->setStatusCode(403)->respondWithError($msssage);
    }

    /**
     * Not Found
     * @param  string $msssage
     * @return mixed
     */
    public function respondNotFound($msssage = 'Not Found!')
    {
        return $this->setStatusCode(404)->respondWithError($msssage);
    }

    /**
     * Unprocessable Entity
     * @param  string $msssage
     * @return mixed
     */
    public function respondUnprocessableEntity($msssage = 'Unprocessable Entity!')
    {
        return $this->setStatusCode(422)->respondWithError($msssage);
    }
}
