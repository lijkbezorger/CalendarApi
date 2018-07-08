<?php

namespace Tests;

use FOS\UserBundle\Model\UserManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use UserBundle\Entity\User;

class ApiTestCaseController extends WebTestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /** @var \Symfony\Bundle\FrameworkBundle\Client */
    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->container = self::bootKernel()->getContainer();
        $this->client = static::createClient();
    }

    protected function createUser($username, $email, $password, $firstName, $lastName)
    {
        $um = $this->container->get('fos_user.user_manager');

        $user = new User();
        $user->setUsername($username);
        $user->setUsernameCanonical($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEnabled(true);
        $user->setRoles(['ROLE_ADMIN']);

        $um->updateUser($user);

        return $user;
    }

    protected function authorize($username, $email)
    {

        try {
            return $this->container->get('lexik_jwt_authentication.encoder.lcobucci')
                ->encode([
                    'username' => $username,
                    'email' => $email,
                    'exp' => time() + 216000
                ]);
        } catch (\Exception $e) {
            return '';
        }
    }
}