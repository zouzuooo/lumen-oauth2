<?php namespace Nord\Lumen\OAuth2\Doctrine\Storage;

use Nord\Lumen\OAuth2\Doctrine\Repositories\SessionRepository;
use Nord\Lumen\OAuth2\Doctrine\Entities\Client;
use Nord\Lumen\OAuth2\Doctrine\Repositories\ClientRepository;
use Doctrine\ORM\EntityManager;
use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\ClientInterface;
use Nord\Lumen\OAuth2\Doctrine\Entities\Session;

class ClientStorage extends DoctrineStorage implements ClientInterface
{

    /**
     * @var ClientRepository
     */
    protected $repository;

    /**
     * @var SessionRepository
     */
    protected $sessionRepository;


    /**
     * ClientStorage constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);

        $this->repository        = $this->entityManager->getRepository(Client::class);
        $this->sessionRepository = $this->entityManager->getRepository(Session::class);
    }


    /**
     * @inheritdoc
     */
    public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        /** @var Client $client */
        $client = $this->repository->findByKey($clientId);

        if ($client === null) {
            return null;
        }

        return $this->createEntity($client);
    }


    /**
     * @inheritdoc
     */
    public function getBySession(SessionEntity $entity)
    {
        /** @var Session $session */
        $session = $this->sessionRepository->find($entity->getId());

        $client = $this->repository->findBySession($session);

        if ($client === null) {
            return null;
        }

        return $this->createEntity($client);
    }


    /**
     * @param Client $client
     *
     * @return \League\OAuth2\Server\Entity\ClientEntity
     */
    protected function createEntity(Client $client)
    {
        $entity = new ClientEntity($this->server);

        $entity->hydrate([
            'id'   => $client->getKey(),
            'name' => $client->getName(),
        ]);

        return $entity;
    }
}
