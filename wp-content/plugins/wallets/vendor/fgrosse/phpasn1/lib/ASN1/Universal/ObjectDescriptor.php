<?php

/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal;

use Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier;
class ObjectDescriptor extends \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Universal\GraphicString
{
    public function __construct($objectDescription)
    {
        parent::__construct($objectDescription);
    }
    public function getType()
    {
        return \Ethereumico\EthereumWallet\Dependencies\FG\ASN1\Identifier::OBJECT_DESCRIPTOR;
    }
}
