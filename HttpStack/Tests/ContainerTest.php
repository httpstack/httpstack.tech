<?php

use PHPUnit\Framework\TestCase;
use HttpStack\Container\Container;
use HttpStack\Exceptions\AppException;

// A dummy class with a dependency
class DatabaseConnection {
    public function __construct(string $dsn) {
        // Assume this would connect to a database
        $this->dsn = $dsn;
    }

    public function getDsn(): string {
        return $this->dsn;
    }
}

// A dummy class with a DatabaseConnection dependency
class UserRepository {
    protected $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function getDatabaseConnection(): DatabaseConnection {
        return $this->db;
    }
}

class ContainerTest extends TestCase {
    private Container $container;

    protected function setUp(): void {
        parent::setUp();
        $this->container = new Container();
    }

    public function testMakeWithParams() {
        $dsn = 'mysql:host=localhost;dbname=test';
        
        // Use the container to create a UserRepository instance,
        // and pass the 'dsn' parameter to the DatabaseConnection constructor.
        // The container automatically handles the dependency resolution.
        $userRepository = $this->container->make(UserRepository::class, ['dsn' => $dsn]);

        // Assert that the returned object is an instance of UserRepository
        $this->assertInstanceOf(UserRepository::class, $userRepository);
        
        // Assert that the DatabaseConnection was correctly injected
        $dbConnection = $userRepository->getDatabaseConnection();
        $this->assertInstanceOf(DatabaseConnection::class, $dbConnection);
        
        // Assert that the parameter was correctly passed to the dependency's constructor
        $this->assertEquals($dsn, $dbConnection->getDsn());
    }
    
    public function testMakeWithoutParamsWithException() {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage("Cannot resolve primitive parameter \$dsn in class DatabaseConnection");
        
        // This should throw an exception because 'dsn' is a primitive type (string)
        // and a value is not provided
        $this->container->make(DatabaseConnection::class);
    }
}