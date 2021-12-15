<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\Epg\Dependencies\Web3\Methods\Personal;

use InvalidArgumentException;
use Ethereumico\Epg\Dependencies\Web3\Methods\EthMethod;
use Ethereumico\Epg\Dependencies\Web3\Validators\AddressValidator;
use Ethereumico\Epg\Dependencies\Web3\Validators\StringValidator;
use Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator;
use Ethereumico\Epg\Dependencies\Web3\Formatters\AddressFormatter;
use Ethereumico\Epg\Dependencies\Web3\Formatters\StringFormatter;
use Ethereumico\Epg\Dependencies\Web3\Formatters\NumberFormatter;
class UnlockAccount extends \Ethereumico\Epg\Dependencies\Web3\Methods\EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [\Ethereumico\Epg\Dependencies\Web3\Validators\AddressValidator::class, \Ethereumico\Epg\Dependencies\Web3\Validators\StringValidator::class, \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::class];
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [\Ethereumico\Epg\Dependencies\Web3\Formatters\AddressFormatter::class, \Ethereumico\Epg\Dependencies\Web3\Formatters\StringFormatter::class, \Ethereumico\Epg\Dependencies\Web3\Formatters\NumberFormatter::class];
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
    protected $defaultValues = [2 => 300];
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
