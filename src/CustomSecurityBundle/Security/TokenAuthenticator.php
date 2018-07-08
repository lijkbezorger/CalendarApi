<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace CustomSecurityBundle\Security;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use UserBundle\Entity\User;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $jwtEncoder;
    private $em;

    public function __construct(JWTEncoderInterface $jwtEncoder, EntityManager $em)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->em = $em;
    }

    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            '',
            'Authorization'
        );

        $token = $extractor->extract($request);

        if (!$token) {
            return '';
        }

        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
        } catch (\Exception $e) {
            $data = false;
        }

        if ($data === false) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }

        $username = $data['username'];

        return $this->em
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        //Not sure if it's right
        $response = new Response(json_encode(['message' => 'Your authentication is failed']), Response::HTTP_UNAUTHORIZED);
        return $response;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
    }
}