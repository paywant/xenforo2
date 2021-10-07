<?php

namespace Paywant\Payment;

use Paywant\Config;
use Paywant\Request;

class Products extends Request
{
    const PATH = '/store/products';

    public function __construct(Config $config)
    {
        parent::__construct($config, self::PATH);
    }

    public function execute($progress_id)
    {
        return parent::get($progress_id);
    }

    public function getResponse($object = false)
    {
        return parent::getResponse($object);
    }

    public function getError($object = false)
    {
        return parent::getError($object);
    }
}
