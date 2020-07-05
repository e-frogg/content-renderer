<?php

declare(strict_types=1);

use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolvableException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolverException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\SolverNotFoundException;
use Efrogg\ContentRenderer\Core\Resolver\Loader\NamespaceSolverLoader;
use PHPUnit\Framework\TestCase;
use Test\Resolver\Solver\Autoload\TestNumberSolver;
use Test\Resolver\Solver\Autoload\TestStringSolver;
use Test\Resolver\TestNumberResolver;
use Test\Resolver\TestSolverInterface;

final class ResolverTest extends TestCase
{
    /**
     * @var TestNumberResolver
     */
    private $resolver;

    public function testInvalidSolverException(): void
    {
        $this->expectException(InvalidSolverException::class);

        $this->resolver->addSolver(
            new \Test\Resolver\Solver\InvalidSolver()
        );
    }

    public function testInvalidSolvableException(): void
    {
        $this->resolver->addSolver(new TestNumberSolver());

        $this->expectException(InvalidSolvableException::class);
        $this->resolver->resolve(new ArrayObject());
    }


    public function testSolverNotFoundException(): void
    {
        $this->resolver->addSolver(new TestNumberSolver());

        $this->expectException(SolverNotFoundException::class);
        $this->resolver->resolve(20);
    }

    public function testResolve(): void
    {
        $this->resolver->addSolver(new TestNumberSolver(0, 10, 0, '0-10'));
        $this->resolver->addSolver(new TestNumberSolver(11, 100, 0, 'sup10'));

        /** @var TestNumberSolver $solver */
        $solver = $this->resolver->resolve(20);
        self::assertInstanceOf(TestNumberSolver::class, $solver);
        self::assertEquals('sup10', $solver->getName());
        $solver = $this->resolver->resolve(5);
        self::assertEquals('0-10', $solver->getName());
    }

    public function testSolverLoader(): void
    {
        $this->resolver->addSolverLoader(
            new NamespaceSolverLoader(__DIR__.'/Test/Resolver/Solver/Autoload', 'Test\\Resolver\\Solver\\Autoload')
        );

        /** @var TestSolverInterface $solver */
        $solverString = $this->resolver->resolve('a string');
        self::assertInstanceOf(TestStringSolver::class, $solverString);
        self::assertEquals('string solver', $solverString->getname());

        $solver = $this->resolver->resolve('5');
        self::assertInstanceOf(TestNumberSolver::class, $solver);
        self::assertEquals('0-10', $solver->getname());

        $this->resolver->foreachSolvers(
            static function(TestSolverInterface $solver) {
            $solver->setName($solver->getName().'!!');
        });

        self::assertEquals('0-10!!', $solver->getname());
        self::assertEquals('string solver!!', $solverString->getname());
    }

    public function testSortableSolvers(): void
    {
        $this->resolver->addSolvers(
            [
                new TestNumberSolver(0, 10, 0, 'A'),
                new TestNumberSolver(0, 10, 2, 'B'),
                new TestNumberSolver(0, 100, 1, 'C'),
                new TestNumberSolver(0, 100, 0, 'D'),
            ]
        );

        /** @var TestNumberSolver $solver */
        $solver = $this->resolver->resolve(5);
        self::assertEquals('B', $solver->getName());
        $solver = $this->resolver->resolve(20);
        self::assertEquals('C', $solver->getName());

        $solvers = $this->resolver->resolveAll(20);
        self::assertCount(2, $solvers);
        $solvers = $this->resolver->resolveAll(7);
        self::assertCount(4, $solvers);
    }

    protected function setUp(): void
    {
        $this->resolver = new TestNumberResolver();
    }
}
