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
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\CallValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\TransactionFormatter;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\OptionalQuantityFormatter;
class Call extends \Ethereumico\EthereumWallet\Dependencies\Web3\Methods\EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [\Ethereumico\EthereumWallet\Dependencies\Web3\Validators\CallValidator::class, [\Ethereumico\EthereumWallet\Dependencies\Web3\Validators\TagValidator::class, \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\QuantityValidator::class]];
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [\Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\TransactionFormatter::class, \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\OptionalQuantityFormatter::class];
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
    protected $defaultValues = [1 => 'latest'];
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
