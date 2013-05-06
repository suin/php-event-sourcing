<?php

namespace Suin\EventSourcing\Example\Domain\Model\User
{
    use Suin\EventSourcing\EventSourcedRootEntity;
    use Suin\EventSourcing\DomainEvent;
    use Suin\EventSourcing\EventStore;
    use Suin\EventSourcing\EventStreamId;

    require_once __DIR__.'/vendor/autoload.php';

    class User
    {
        use EventSourcedRootEntity;

        private $id;
        private $name;
        private $email;

        public function __construct($id, $name, $email)
        {
            $this->apply(new UserRegistered($id, $name, $email));
        }

        public function id()
        {
            return $this->id;
        }

        public function changeEmail($email)
        {
            $this->apply(new UserEmailChanged($this->id, $email));
        }

        public function whenUserRegistered(UserRegistered $event)
        {
            $this->id = $event->userId();
            $this->name = $event->name();
            $this->email = $event->email();
        }

        public function whenUserEmailChanged(UserEmailChanged $event)
        {
            $this->email = $event->email();
        }
    }

    class UserRegistered implements DomainEvent
    {
        private $userId;
        private $name;
        private $email;

        public function __construct($userId, $name, $email)
        {
            $this->userId = $userId;
            $this->name = $name;
            $this->email = $email;
        }

        public function userId()
        {
            return $this->userId;
        }

        public function name()
        {
            return $this->name;
        }

        public function email()
        {
            return $this->email;
        }

        public function occurredOn()
        {
        }
    }

    class UserEmailChanged implements DomainEvent
    {
        private $userId;
        private $email;

        public function __construct($userId, $email)
        {
            $this->userId = $userId;
            $this->email = $email;
        }

        public function userId()
        {
            return $this->userId;
        }

        public function email()
        {
            return $this->email;
        }

        public function occurredOn()
        {
        }
    }

    interface UserRepository
    {
        /**
         * @return string
         */
        public function nextIdentity();

        /**
         * @param string $userId
         * @return User
         */
        public function userOfId($userId);

        /**
         * @param User $user
         * @return void
         */
        public function save(User $user);
    }

    class EventStoreUserRepository implements UserRepository
    {
        /**
         * @var EventStore
         */
        private $eventStore;

        /**
         * @param EventStore $eventStore
         */
        public function __construct(EventStore $eventStore)
        {
            $this->eventStore = $eventStore;
        }

        /**
         * @return string
         */
        public function nextIdentity()
        {
            return md5(uniqid());
        }

        /**
         * @param string $userId
         * @return User
         */
        public function userOfId($userId)
        {
            $eventStreamId = new EventStreamId($userId);
            $eventStream = $this->eventStore->eventStreamSince($eventStreamId);

            return User::constructWithEventStream($eventStream->events(), $eventStream->version());
        }

        /**
         * @param User $user
         * @return void
         */
        public function save(User $user)
        {
            $eventId = new EventStreamId($user->id(), $user->mutatedVersion());
            $this->eventStore->appendWith($eventId, $user->mutatingEvents());
        }
    }
}

namespace Suin\EventSourcing\Application
{
    use PDO;
    use Suin\EventSourcing\Example\Domain\Model\User\EventStoreUserRepository;
    use Suin\EventSourcing\Example\Domain\Model\User\User;
    use Suin\EventSourcing\MySQLPDOEventStore;

    require_once __DIR__.'/vendor/autoload.php';

    $pdo = new PDO('mysql:host=localhost;dbname=test', 'root', 'root', [
        PDO::ATTR_ORACLE_NULLS       => PDO::NULL_NATURAL, // NULL is available
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_AUTOCOMMIT         => true,
    ]);
    $pdo->exec('DROP TABLE IF EXISTS tbl_es_event_store');
    $pdo->exec(file_get_contents(__DIR__.'/src/Suin/EventSourcing/Resources/event_store.sql'));
    $eventStore = new MySQLPDOEventStore($pdo);
    $userRepository = new EventStoreUserRepository($eventStore);

    // Persistence example in Application Service
    $userAggregateId = $userRepository->nextIdentity();
    $user = new User($userAggregateId, 'Alice', 'alice@example.com');
    $user->changeEmail('alice.brown@example.com');
    $userRepository->save($user);

    // Query example in Application Service
    $user = $userRepository->userOfId($userAggregateId);
    var_dump($user);
}
