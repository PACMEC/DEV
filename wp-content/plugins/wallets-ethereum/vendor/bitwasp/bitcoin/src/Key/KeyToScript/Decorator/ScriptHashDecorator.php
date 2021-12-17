<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Decorator;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\DisallowedScriptDataFactoryException;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\KeyToScriptDataFactory;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory;
abstract class ScriptHashDecorator extends \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory
{
    /**
     * @var KeyToScriptDataFactory
     */
    protected $scriptDataFactory;
    /**
     * @var string[]
     */
    protected $allowedScriptTypes = [];
    /**
     * @var string
     */
    protected $decorateType;
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript\Factory\KeyToScriptDataFactory $scriptDataFactory)
    {
        if (!\in_array($scriptDataFactory->getScriptType(), $this->allowedScriptTypes, \true)) {
            throw new \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Exceptions\DisallowedScriptDataFactoryException("Unsupported key-to-script factory for this script-hash type.");
        }
        $this->scriptDataFactory = $scriptDataFactory;
    }
    /**
     * @return string
     */
    public function getScriptType() : string
    {
        return \sprintf("%s|%s", $this->decorateType, $this->scriptDataFactory->getScriptType());
    }
}
