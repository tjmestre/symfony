<?php

/*
 * This file is part of the Symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Component\Security\Authorization\Voter;

use Symfony\Component\Security\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Role\Role;

class RoleVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getVoteTests
     */
    public function testVote($roles, $attributes, $expected)
    {
        $voter = new RoleVoter();

        $this->assertSame($expected, $voter->vote($this->getToken($roles), null, $attributes));
    }

    public function getVoteTests()
    {
        return array(
            array(array(), array(), VoterInterface::ACCESS_ABSTAIN),
            array(array(), array('FOO'), VoterInterface::ACCESS_ABSTAIN),
            array(array(), array('ROLE_FOO'), VoterInterface::ACCESS_DENIED),
            array(array('ROLE_FOO'), array('ROLE_FOO'), VoterInterface::ACCESS_GRANTED),
            array(array('ROLE_FOO'), array('FOO', 'ROLE_FOO'), VoterInterface::ACCESS_GRANTED),
            array(array('ROLE_BAR', 'ROLE_FOO'), array('ROLE_FOO'), VoterInterface::ACCESS_GRANTED),
        );
    }

    protected function getToken(array $roles)
    {
        foreach ($roles as $i => $role) {
            $roles[$i] = new Role($role);
        }
        $token = $this->getMock('Symfony\Component\Security\Authentication\Token\TokenInterface');
        $token->expects($this->once())
              ->method('getRoles')
              ->will($this->returnValue($roles));
        ;

        return $token;
    }
}