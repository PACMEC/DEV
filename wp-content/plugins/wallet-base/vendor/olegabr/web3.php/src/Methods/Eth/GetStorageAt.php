<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\EthereumWallet\Dependencies\Web3\Methods\Eth;

use InvalidArgumentException;
use Ethereumico\EthereumWallet\Dependencies\Web3\Methods\EthMethod;
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\TagValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\QuantityValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\AddressValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\AddressFormatter;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\OptionalQuantityFormatter;
class GetStorageAt extends EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [AddressValidator::class, QuantityValidator::class, [TagValidator::class, QuantityValidator::class]];
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [AddressFormatter::class, QuantityFormatter::class, OptionalQuantityFormatter::class];
    /**
     * outputFormatters
     * 
     * @var array
     */
    protected $outputFormatters = [];
    /**
     * defaultValues
     * 
     * @var array
     */
    protected $defaultValues = [2 => 'latest'];
    /**
     * construct
     * 
     * @param string $method
     * @param array $arguments
     * @return void
     */
    // public function __construct($method='', $arguments=[])
    // {
    //     parent::__construct($method, $arguments);
    // }
}
