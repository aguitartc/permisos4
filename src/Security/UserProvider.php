<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;


class UserProvider implements UserProviderInterface
{
	protected $userRepository;
	
	public function __construct(ObjectRepository $userRepository)
	{
	    $this->userRepository = $userRepository;
	}
	
	/**
     * Provides the authenticated user a ROLE_USER
     * @param $username
     * @return User
     * @throws UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {	
        if($username)
        {
             $user = $this->userRepository->loadUserByUsername($username);
             if(!empty($user))
                return $user;
        }
        
        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    /**
     * @param UserInterface $user
     * @return User
     * @throws UnsupportedUserException
     * @throws UsernameNotFoundException
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param $class
     * @return bool
     */
    public function supportsClass($class)
    {   
        return ($class === 'App\Entity\User');
    }
}
