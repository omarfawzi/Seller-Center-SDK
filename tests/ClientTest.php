<?php
/**
 * Created by PhpStorm.
 * User: salama
 * Date: 08/01/19
 * Time: 11:28 ص
 */

namespace SellerCenter\tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use SellerCenter\Exception\SellerCenterException;
use SellerCenter\Handler\ResponseHandler;
use SellerCenter\Http\Client;
use SellerCenter\Model\Configuration;
use SellerCenter\Model\Request;

class ClientTest extends TestCase
{
    /** @var ResponseHandler $responseHandler */
    protected $responseHandler;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct(
            $name,
            $data,
            $dataName
        );
        $this->responseHandler = new ResponseHandler();
    }

    /**
     * @dataProvider getClientSendRequestTestCases
     *
     * @param string $responseBody
     * @param int    $case
     * @param int    $statusCode
     *
     * @throws SellerCenterException
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function testClientSendRequest(string $responseBody, int $case, int $statusCode = 200)
    {
        $validRequest = json_decode(file_get_contents(__DIR__.'/ClientTest/Valid/valid_request.json'), true);
        $configuration = new Configuration(
            $validRequest['url'],
            $validRequest['email'],
            $validRequest['apiKey'],
            $validRequest['apiPassword'],
            $validRequest['username'],
            $validRequest['version']
        );
        if ($case < 4) {
            $guzzleMock = m::mock(GuzzleClient::class)
                ->allows('request')
                ->andReturn(
                    new Response($statusCode, [], $responseBody)
                )
                ->getMock();
        } else {
            $guzzleMock = m::mock(GuzzleClient::class)
                ->allows('request')
                ->andReturnUsing(
                    function () use ($statusCode, $responseBody) {
                        throw new BadResponseException(
                            null, $this->createMock(GuzzleRequest::class), new Response(
                                $statusCode, [
                                'Content-Type' => [
                                    0 => 'application/json',
                                ],
                            ], $responseBody
                            )
                        );
                    }
                )
                ->getMock();
        }
        $configuration->setMaxAttemptsDelay(0);
        $configuration->setMinAttemptsDelay(0);
        $client = new Client();
        $reflection       = new ReflectionClass($client);
        $guzzleProperty = $reflection->getProperty('httpClient');
        $guzzleProperty->setAccessible(true);
        $guzzleProperty->setValue($client, $guzzleMock);
        $request = $this->formSellerCenterRequest($validRequest);
        switch ($case) {
            case 1 :
                $response = $client->sendSellerCenterRequest($configuration,$request);
                $this->assertInstanceOf(GuzzleResponse::class, $response);
                break;
            case 2 :
                $this->expectException(SellerCenterException::class);
                $client->sendSellerCenterRequest($configuration,$request);
                break;
            case 3 :
                $this->expectException(SellerCenterException::class);
                $client->sendSellerCenterRequest($configuration,$request);
                break;
            case 4 :
                $response = $client->sendSellerCenterRequest($configuration,$request);
                $this->assertInstanceOf(GuzzleResponse::class, $response);
                break;
            case 5 :
                $this->expectException(SellerCenterException::class);
                $client->sendSellerCenterRequest($configuration,$request);
                break;
            default :
                break;
        }
    }

    public function formSellerCenterRequest($data): Request
    {
        $sellerCenterRequest = new Request();
        $sellerCenterRequest->setBaseUrl($data['url']);
        $sellerCenterRequest->setUserId($data['email']);
        $sellerCenterRequest->setApiKey($data['apiKey']);
        $sellerCenterRequest->setPassword($data['apiPassword']);
        $sellerCenterRequest->setVersion($data['version']);
        $sellerCenterRequest->setUsername($data['username']);
        $sellerCenterRequest->setAction($data['action']);

        return $sellerCenterRequest;
    }

    public function getClientSendRequestTestCases()
    {
        $response = json_decode(file_get_contents(__DIR__.'/ClientTest/response.json'), true);

        return [
            [
                json_encode($response['test1']),
                1,
            ],
            [
                json_encode($response['test2']),
                2,
            ],
            [
                json_encode($response['test3']),
                3,
            ],
            [
                json_encode($response['test1']),
                4,
            ],
            [
                json_encode($response['test2']),
                5,
            ],
        ];
    }


}