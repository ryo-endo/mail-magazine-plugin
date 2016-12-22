<?php
namespace Plugin\MailMagazine\Tests;

use Eccube\Common\Constant;
use Eccube\Tests\EccubeTestCase;
use Plugin\MailMagazine\Entity\MailmagaCustomer;
use Plugin\MailMagazine\Service\MailMagazineService;
use Plugin\MailMagazine\Util\Version;

abstract class AbstractMailMagazineTestCase extends EccubeTestCase
{
    /**
     * @var MailMagazineService $mailMagazineService
     */
    protected $mailMagazineService;

    /**
     * @var MailMagazineSendHistoryRepository $mailMagazineSendHistoryRepository
     */
    protected $mailMagazineSendHistoryRepository;

    public function setUp()
    {
        parent::setUp();
        
        if (!Version::isSupport()) {
            $this->app->loadPlugin();
        }
        
        $this->mailMagazineService = $this->app['eccube.plugin.mail_magazine.service.mail'];
        $this->mailMagazineSendHistoryRepository = $this->app[MailMagazineService::REPOSITORY_SEND_HISTORY];
    }

    /**
     * @param string $email
     * @param string $name01
     * @param string $name02
     * @return \Eccube\Entity\Customer
     */
    protected function createMailmagaCustomer($email = 'mail_magazine_service_test@example.com', $name01 = 'name01', $name02 = 'name02')
    {
        $c = $this->createCustomer($email);
        if ($name01) $c->setName01($name01);
        if ($name02) $c->setName02($name02);
        $this->app['orm.em']->persist($c);
        $this->app['orm.em']->flush($c);

        $mc = new MailmagaCustomer();
        $mc->setCustomerId($c->getId());
        $mc->setDelFlg(Constant::DISABLED);
        $mc->setMailmagaFlg('1');

        $this->app['orm.em']->persist($mc);
        $this->app['orm.em']->flush($mc);

        return $c;
    }

    protected function createHistory(\Eccube\Entity\Customer $Customer)
    {
        return $this->mailMagazineService->createMailMagazineHistory(array(
            'subject' => 'subject',
            'body' => 'body',
            'multi' => $Customer->getEmail(),
        ));
    }
}