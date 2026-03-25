<?php

namespace Modules\Desktopapp\Http\Controllers\Api;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Illuminate\Routing\Controller;

class ApiController extends Controller
{
    protected $statusCode;
    protected $perPage;

    public function __construct() {
        $this->perPage = 10;
    }

    public function getStatusCode(){
        return $this->statusCode;
    }

    public function setStatusCode($statusCode){
        $this->statusCode = $statusCode;
        return $this;
    }

    public function respondUnauthorized($message = 'Unauthorized action.'){
        return $this->setStatusCode(403)->respondWithError($message);
    }

    public function respond($data){
        return response()->json($data);
    }

    public function modelNotFoundExceptionResult($e) {
        return $this->setStatusCode(404)->respondWithError($e->getMessage());

        // return [
        //         'status' => 404,
        //         'class' => method_exists($e, 'getModel') ? $e->getModel() : '',
        //         'value' => method_exists($e, 'getIds') ? $e->getIds() : '',
        //         'message' => 
        //     ];
    }

    public function otherExceptions($e) {
        $msg = is_object($e) ? $e->getMessage() : $e;
        return $this->setStatusCode(400)->respondWithError($msg);

        // return [
        //         'status' => 400,
        //         'message' => $e->getMessage()
        //     ];
    }

    protected function respondWithError($message){
        return response()->json([
                'error' => [
                    'message' => $message
                ]
            ], $this->getStatusCode());
    }

    /**
     * Retrieves current passport client from request
     */
   public function getClient()
{
    $bearerToken = request()->bearerToken();

    if (!$bearerToken) {
        return $this->respondUnauthorized('Missing bearer token.');
    }

    $token = (new Parser(new JoseEncoder()))->parse($bearerToken);
    $tokenId = $token->claims()->get('jti'); // claim instead of header

 
$client = $token ? $token->client : null;

    return $client ?? $this->respondUnauthorized('Client not found.');
}
}
