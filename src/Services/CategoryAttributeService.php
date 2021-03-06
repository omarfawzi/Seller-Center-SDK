<?php
/**
 * Created by PhpStorm.
 * User: salama
 * Date: 15/01/19
 * Time: 02:02 م
 */

namespace SellerCenter\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;
use SellerCenter\Exception\SellerCenterException;
use SellerCenter\Model\Configuration;
use SellerCenter\Model\CategoryAttribute;
use SellerCenter\Model\Request;
use SellerCenter\Proxy\SellerCenterProxy;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryAttributeService
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
     * @param int           $categoryId
     *
     * @return CategoryAttribute[] array
     * @throws GuzzleException
     * @throws SellerCenterException
     */
    public function getCategoryAttributes(Configuration $configuration, int $categoryId): array
    {
        $sellerCenterRequest = new Request();
        $sellerCenterRequest->setParameters(
            [
                Request::QUERY_PARAMETER_ACTION           => Request::ACTION_GET_CATEGORY_ATTRIBUTES,
                Request::QUERY_PARAMETER_PRIMARY_CATEGORY => $categoryId,
            ]
        );
        $sellerCenterRequest->setAction(Request::ACTION_GET_CATEGORY_ATTRIBUTES);
        $sellerCenterRequest->addConfiguration($configuration);
        return $this->sellerCenterProxy->getResponse($configuration, $sellerCenterRequest)->getBody();
    }
}