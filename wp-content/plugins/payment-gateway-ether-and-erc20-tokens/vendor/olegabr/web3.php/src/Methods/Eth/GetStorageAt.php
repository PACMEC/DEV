<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\Epg\Dependencies\Web3\Methods\Eth;

use InvalidArgumentException;
use Ethereumico\Epg\Dependencies\Web3\Methods\EthMethod;
use Ethereumico\Epg\Dependencies\Web3\Validators\TagValidator;
use Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator;
use Ethereumico\Epg\Dependencies\Web3\Validators\AddressValidator;
use Ethereumico\Epg\Dependencies\Web3\Formatters\AddressFormatter;
use Ethereumico\Epg\Dependencies\Web3\Formatters\QuantityFormatter;
use Ethereumico\Epg\Dependencies\Web3\Formatters\OptionalQuantityFormatter;
class GetStorageAt extends \Ethereumico\Epg\Dependencies\Web3\Methods\EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [\Ethereumico\Epg\Dependencies\Web3\Validators\AddressValidator::class, \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::class, [\Ethereumico\Epg\Dependencies\Web3\Validators\TagValidator::class, \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::class]];
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [\Ethereumico\Epg\Dependencies\Web3\Formatters\AddressFormatter::class, \Ethereumico\Epg\Dependencies\Web3\Formatters\QuantityFormatter::class, \Ethereumico\Epg\Dependencies\Web3\Formatters\OptionalQuantityFormatter::class];
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
