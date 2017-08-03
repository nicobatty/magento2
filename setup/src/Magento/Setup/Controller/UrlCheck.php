<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Magento\Framework\Validator\Url as UrlValidator;

/**
 * Class \Magento\Setup\Controller\UrlCheck
 *
 * @since 2.2.0
 */
class UrlCheck extends AbstractActionController
{
    /**
     * @var UrlValidator
     * @since 2.2.0
     */
    private $urlValidator;

    /**
     * @param UrlValidator $urlValidator
     * @since 2.2.0
     */
    public function __construct(UrlValidator $urlValidator)
    {
        $this->urlValidator = $urlValidator;
    }

    /**
     * Validate URL
     *
     * @return JsonModel
     * @since 2.2.0
     */
    public function indexAction()
    {
        $params = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        $result = ['successUrl' => false, 'successSecureUrl' => true];

        $hasBaseUrl = isset($params['address']['actual_base_url']);
        $hasSecureBaseUrl = isset($params['https']['text']);
        $hasSecureAdminUrl = !empty($params['https']['admin']);
        $hasSecureFrontUrl = !empty($params['https']['front']);
        $schemes = ['http', 'https'];

        // Validating of Base URL
        if ($hasBaseUrl && $this->urlValidator->isValid($params['address']['actual_base_url'], $schemes)) {
            $result['successUrl'] = true;
        }

        // Validating of Secure Base URL
        if ($hasSecureAdminUrl || $hasSecureFrontUrl) {
            if (!($hasSecureBaseUrl && $this->urlValidator->isValid($params['https']['text'], $schemes))) {
                $result['successSecureUrl'] = false;
            }
        }

        return new JsonModel($result);
    }
}