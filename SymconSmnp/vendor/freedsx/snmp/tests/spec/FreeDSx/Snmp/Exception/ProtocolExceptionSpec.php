<?php
/**
 * This file is part of the FreeDSx SNMP package.
 *
 * (c) Chad Sikorra <Chad.Sikorra@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FreeDSx\Snmp\Exception;

use FreeDSx\Snmp\Exception\ProtocolException;
use PhpSpec\ObjectBehavior;

class ProtocolExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProtocolException::class);
    }

    function it_should_extend_exception()
    {
        $this->shouldBeAnInstanceOf(\Exception::class);
    }
}
