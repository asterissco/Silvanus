<?
namespace Silvanus\FirewallRulesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Silvanus\FirewallRulesBundle\Entity\FirewallRules;

class LoadUserData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $rule = new FirewallRules();
        $rule->setRule('iptables -A INPUT -s 0.0.0.0/0 -d 192.168.100.25 -p TCP -j ACCEPT');
        $rule->setPriority('1');
        $rule->setChainId('1');
        $manager->persist($rule);

        $rule = new FirewallRules();
        $rule->setRule('iptables -A INPUT -s 0.0.0.0/0 -d 192.168.100.25 -p UDP -j ACCEPT');
        $rule->setPriority('2');
        $rule->setChainId('1');
        $manager->persist($rule);

        $rule = new FirewallRules();
        $rule->setRule('iptables -A INPUT -s 80.25.36.5 -d 192.168.100.25 -p TCP -j DROP');
        $rule->setPriority('3');
        $rule->setChainId('1');
        $manager->persist($rule);

        $rule = new FirewallRules();
        $rule->setRule('iptables -A OUTPUT -s 78.25.3.68 -d 192.168.100.25 -p TCP -j DROP');
        $rule->setPriority('4');
        $rule->setChainId('1');
        $manager->persist($rule);

        $rule = new FirewallRules();
        $rule->setRule('iptables -A OUTPUT -s 80.0.2.54 -d 192.168.100.25 -p TCP -j ACCEPT');
        $rule->setPriority('5');
        $rule->setChainId('1');
        $manager->persist($rule);

        $rule = new FirewallRules();
        $rule->setRule('iptables -A OUTPUT -s 98.65.125.25 -d 192.168.100.25 -p TCP --dport 30 -j ACCEPT');
        $rule->setPriority('6');
        $rule->setChainId('1');
        $manager->persist($rule);

        $rule = new FirewallRules();
        $rule->setRule('iptables -A OUTPUT -s 98.65.125.25 -d 192.168.100.89 -p TCP --dport 14 -j ACCEPT');
        $rule->setPriority('7');
        $rule->setChainId('1');
        $manager->persist($rule);


        $manager->flush();
    }
}
