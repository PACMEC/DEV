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
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\AddressValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\HexValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\AddressFormatter;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\HexFormatter;
class Sign extends \Ethereumico\EthereumWallet\Dependencies\Web3\Methods\EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [\Ethereumico\EthereumWallet\Dependencies\Web3\Validators\AddressValidator::class, \Ethereumico\EthereumWallet\Dependencies\Web3\Validators\HexValidator::class];
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [\Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\AddressFormatter::class, \Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\HexFormatter::class];
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
    protected $defaultValues = [];
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
