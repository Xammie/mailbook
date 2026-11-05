<?php

declare(strict_types=1);

namespace Xammie\Mailbook\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Xammie\Mailbook\Facades\Mailbook;
use Xammie\Mailbook\Mailbook as MailbookClass;
use Xammie\Mailbook\MailRegistrar;

/**
 * Adds a return type to closures passed to Mailbook::add(), based on the mailable
 * instance they return.
 *
 * Mailbook uses the closure return type to resolve the class name of a mail through
 * reflection, without having to build the mailable instance through the container.
 * When the return type is missing Mailbook needs to call the closure to know which
 * mail it registers, which makes rendering the mailbook page significantly slower.
 *
 * ```php
 * // before
 * Mailbook::add(function () {
 *     $user = User::factory()->make();
 *
 *     return new VerificationMail($user, '/example/url');
 * });
 *
 * // after
 * Mailbook::add(function (): VerificationMail {
 *     $user = User::factory()->make();
 *
 *     return new VerificationMail($user, '/example/url');
 * });
 * ```
 *
 * Closures are only rewritten when every return statement creates an instance of the
 * same class with `new`. When a closure returns different classes, returns something
 * that is not a `new` expression, or already has a return type it is left untouched.
 */
final class AddMailbookClosureReturnTypeRector extends AbstractRector
{
    /**
     * The classes a static Mailbook::add() call can be made on.
     *
     * @var string[]
     */
    private const MAILBOOK_CLASSES = [Mailbook::class, MailbookClass::class];

    public function getNodeTypes(): array
    {
        return [StaticCall::class, MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        // Rector only dispatches the node types from getNodeTypes(), this narrows the type for static analysis
        if (! $node instanceof StaticCall && ! $node instanceof MethodCall) {
            return null;
        }

        if (! $this->isMailbookAddCall($node)) {
            return null;
        }

        $closure = $this->matchClosureArgument($node->getRawArgs());

        if ($closure === null) {
            return null;
        }

        if ($closure->returnType instanceof Node) {
            return null;
        }

        $returnType = $this->matchReturnType($closure);

        if (! $returnType instanceof Name) {
            return null;
        }

        $closure->returnType = $returnType;

        return $node;
    }

    private function isMailbookAddCall(StaticCall|MethodCall $node): bool
    {
        if (! $this->isName($node->name, 'add')) {
            return false;
        }

        if ($node instanceof StaticCall) {
            return $node->class instanceof Name
                && $this->isNames($node->class, self::MAILBOOK_CLASSES);
        }

        return $this->isObjectType($node->var, new ObjectType(MailbookClass::class))
            || $this->isObjectType($node->var, new ObjectType(MailRegistrar::class));
    }

    /**
     * @param  array<int, Node>  $args
     */
    private function matchClosureArgument(array $args): Closure|ArrowFunction|null
    {
        $firstArg = $args[0] ?? null;

        if (! $firstArg instanceof Arg) {
            return null;
        }

        if ($firstArg->name instanceof Identifier && ! $this->isName($firstArg->name, 'class')) {
            return null;
        }

        if ($firstArg->value instanceof Closure) {
            return $firstArg->value;
        }

        if ($firstArg->value instanceof ArrowFunction) {
            return $firstArg->value;
        }

        return null;
    }

    private function matchReturnType(Closure|ArrowFunction $closure): ?Name
    {
        $classNames = [];

        foreach ($this->collectReturnedExpressions($closure) as $returnedExpression) {
            if (! $returnedExpression instanceof New_) {
                return null;
            }

            if (! $returnedExpression->class instanceof Name) {
                return null;
            }

            $className = strtolower($this->getName($returnedExpression->class));

            if (in_array($className, ['self', 'static', 'parent'], true)) {
                return null;
            }

            // the union operator keeps the name node of the first return statement,
            // so the inserted return type is written in the style it is returned in
            $classNames += [$className => $returnedExpression->class];
        }

        if (count($classNames) !== 1) {
            return null;
        }

        return clone array_values($classNames)[0];
    }

    /**
     * Collects the expressions the closure returns, leaving out return statements
     * belonging to functions nested within it.
     *
     * @return array<int, Expr|null>
     */
    private function collectReturnedExpressions(Closure|ArrowFunction $closure): array
    {
        if ($closure instanceof ArrowFunction) {
            return [$closure->expr];
        }

        $returnedExpressions = [];

        $this->traverseNodesWithCallable($closure->stmts, function (Node $node) use (&$returnedExpressions): ?int {
            if ($this->isNestedFunctionLike($node)) {
                return NodeVisitor::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }

            if ($node instanceof Return_) {
                $returnedExpressions[] = $node->expr;
            }

            return null;
        });

        return $returnedExpressions;
    }

    /**
     * A return statement inside a nested function like does not belong to the
     * closure passed to Mailbook::add().
     */
    private function isNestedFunctionLike(Node $node): bool
    {
        if ($node instanceof Closure) {
            return true;
        }

        if ($node instanceof Function_) {
            return true;
        }

        if ($node instanceof ClassMethod) {
            return true;
        }

        return $node instanceof ClassLike;
    }
}
