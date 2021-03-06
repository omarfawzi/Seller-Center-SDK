<?php
/**
 * Created by PhpStorm.
 * User: salama
 * Date: 15/01/19
 * Time: 01:51 م
 */

namespace SellerCenter\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;
use SellerCenter\Exception\SellerCenterException;
use SellerCenter\Model\Configuration;
use SellerCenter\Model\FeedStatus;
use SellerCenter\Model\Request;
use SellerCenter\Proxy\SellerCenterProxy;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FeedService
{
    /** @var SellerCenterProxy $sellerCenterProxy */
    protected $sellerCenterProxy;

    /** @var ValidatorInterface $validator */
    protected $validator;

    public function __construct()
    {
        $this->sellerCenterProxy = new SellerCenterProxy();
        $this->validator         = new Validator();
    }

    /**
     * @param Configuration $configuration
     * @param string        $feedId
     *
     * @return FeedStatus
     * @throws GuzzleException
     * @throws SellerCenterException
     */
    public function getFeedStatus(Configuration $configuration, string $feedId): FeedStatus
    {
        $sellerCenterRequest = new Request();
        $sellerCenterRequest->setParameters(
            [
                Request::QUERY_PARAMETER_ACTION  => Request::ACTION_GET_FEED_STATUS,
                Request::QUERY_PARAMETER_FEED_ID => $feedId,
            ]
        );
        $sellerCenterRequest->setAction(Request::ACTION_GET_FEED_STATUS);
        $sellerCenterRequest->addConfiguration($configuration);

        return $this->sellerCenterProxy->getResponse($configuration,$sellerCenterRequest)->getBody();
    }
}