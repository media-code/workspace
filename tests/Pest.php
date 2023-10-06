<?php

use Gedachtegoed\Workspace\Core\Concerns\InteractsWithDirectories;
use Gedachtegoed\Workspace\Core\Concerns\UpdatesGitignore;
use Gedachtegoed\Workspace\Tests;

use function Orchestra\Testbench\package_path;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(Tests\TestCase::class)
    ->in('Unit', 'Integration');

uses(Tests\TestCase::class)
    ->group('integration')
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

function expectFileContents($path)
{
    return expect(file_get_contents(base_path($path)));
}

function expectFileExists($path)
{
    return expect(file_exists(base_path($path)));
}

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function register(...$args)
{
    config(['workspace-integrations' => [
        ...$args,
    ]]);
}

function pugreSkeleton()
{
    $purgeSkeleton = new class
    {
        use InteractsWithDirectories;

        public function __construct()
        {
            $this->replaceDirectory(
                package_path('tests/workbench-skeleton'),
                package_path('skeleton')
            );
        }
    };

    new $purgeSkeleton;
}

function gitignore()
{
    return new class
    {
        use UpdatesGitignore;
    };
}
