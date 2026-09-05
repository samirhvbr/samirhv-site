<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * The base for every test here.
 *
 * Deliberately empty, and worth saying why: the suite uses no RefreshDatabase
 * and no DatabaseTransactions trait anywhere, because it needs no database.
 * phpunit.xml points DB_DATABASE at one that does not exist, so a test that
 * reaches for a database fails loudly instead of quietly reading and writing
 * the development one. A test that genuinely needs a database should create it
 * and say so, rather than have this class hand one to everybody.
 */
abstract class TestCase extends BaseTestCase
{
    //
}
