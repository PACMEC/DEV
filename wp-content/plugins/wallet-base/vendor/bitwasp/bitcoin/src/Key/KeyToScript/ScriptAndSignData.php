<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Key\KeyToScript;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\Address;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Address\BaseAddressCreator;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Script\ScriptInterface;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\SignData;
class ScriptAndSignData
{
    /**
     * @var ScriptInterface
     */
    private $scriptPubKey;
    /**
     * @var SignData
     */
    private $signData;
    /**
     * ScriptAndSignData constructor.
     * @param ScriptInterface $scriptPubKey
     * @param SignData $signData
     */
    public function __construct(ScriptInterface $scriptPubKey, SignData $signData)
    {
        $this->scriptPubKey = $scriptPubKey;
        $this->signData = $signData;
    }
    /**
     * @return ScriptInterface
     */
    public function getScriptPubKey() : ScriptInterface
    {
        return $this->scriptPubKey;
    }
    /**
     * @param BaseAddressCreator $creator
     * @return Address
     */
    public function getAddress(BaseAddressCreator $creator) : Address
    {
        return $creator->fromOutputScript($this->scriptPubKey);
    }
    /**
     * @return SignData
     */
    public function getSignData() : SignData
    {
        return $this->signData;
    }
}
