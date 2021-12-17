<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Serializer\Signature\Der;

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Integer;
use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\SignatureInterface;
class Formatter
{
    /**
     * @param SignatureInterface $signature
     * @return Sequence
     */
    public function toAsn(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\SignatureInterface $signature) : \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence
    {
        return new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Sequence(new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Integer(\gmp_strval($signature->getR(), 10)), new \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\Integer(\gmp_strval($signature->getS(), 10)));
    }
    /**
     * @param SignatureInterface $signature
     * @return string
     */
    public function serialize(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Crypto\Signature\SignatureInterface $signature) : string
    {
        return $this->toAsn($signature)->getBinary();
    }
}
