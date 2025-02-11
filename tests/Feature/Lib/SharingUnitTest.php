<?php

/**
 * We don't care for unhandled exceptions in tests.
 * It is the nature of a test to throw an exception.
 * Without this suppression we had 100+ Linter warning in this file which
 * don't help anything.
 *
 * @noinspection PhpDocMissingThrowsInspection
 * @noinspection PhpUnhandledExceptionInspection
 */

namespace Tests\Feature\Lib;

use Illuminate\Testing\TestResponse;
use Tests\Feature\Traits\CatchFailures;
use Tests\TestCase;

class SharingUnitTest
{
	use CatchFailures;

	private TestCase $testCase;

	public function __construct(TestCase $testCase)
	{
		$this->testCase = $testCase;
	}

	/**
	 * List shares.
	 *
	 * @param int         $expectedStatusCode
	 * @param string|null $assertSee
	 *
	 * @return TestResponse
	 */
	public function list(
		int $expectedStatusCode = 200,
		?string $assertSee = null
	): TestResponse {
		$response = $this->testCase->postJson('/api/Sharing::list');
		$this->assertStatus($response, $expectedStatusCode);
		if ($assertSee) {
			$response->assertSee($assertSee, false);
		}

		return $response;
	}

	/**
	 * List shares.
	 *
	 * @param string[]    $albumIDs
	 * @param int[]       $userIDs
	 * @param int         $expectedStatusCode
	 * @param string|null $assertSee
	 *
	 * @return TestResponse
	 */
	public function add(
		array $albumIDs,
		array $userIDs,
		int $expectedStatusCode = 204,
		?string $assertSee = null
	): TestResponse {
		$response = $this->testCase->postJson(
			'/api/Sharing::add', [
				'albumIDs' => $albumIDs,
				'userIDs' => $userIDs,
			]);
		$this->assertStatus($response, $expectedStatusCode);
		if ($assertSee) {
			$response->assertSee($assertSee, false);
		}

		return $response;
	}
}
