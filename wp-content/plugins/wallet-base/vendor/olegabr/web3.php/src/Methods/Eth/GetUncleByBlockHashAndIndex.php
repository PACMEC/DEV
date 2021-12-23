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
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\BlockHashValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\QuantityValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\HexFormatter;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\QuantityFormatter;
class GetUncleByBlockHashAndIndex extends EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [BlockHashValidator::class, QuantityValidator::class];
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [HexFormatter::class, QuantityFormatter::class];
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
