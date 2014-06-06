<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martinhalamicek
 * Date: 22/05/14
 * Time: 16:38
 * To change this template use File | Settings | File Templates.
 */

use Keboola\Csv\CsvFile;

class Keboola_StorageApi_Tables_CreateTest extends StorageApiTestCase
{


	public function setUp()
	{
		parent::setUp();
		$this->_initEmptyBucketsForAllBackends();
	}

	/**
	 * @dataProvider tableCreateData
	 * @param $langugesFile
	 */
	public function testTableCreate($langugesFile, $expectationFile, $async, $backend, $options = array())
	{
		$createMethod = $async ? 'createTableAsync' : 'createTable';
		$tableId = $this->_client->{$createMethod}(
			$this->getTestBucketId(self::STAGE_IN, $backend),
			'Languages',
			new CsvFile($langugesFile),
			$options
		);
		$table = $this->_client->getTable($tableId);

		$expectationFileCsv = new CsvFile($expectationFile);

		$this->assertEquals($tableId, $table['id']);
		$this->assertEquals('Languages', $table['name']);
		$this->assertNotEmpty($table['created']);
		$this->assertNotEmpty($table['lastChangeDate']);
		$this->assertNotEmpty($table['lastImportDate']);
		$this->assertEquals($expectationFileCsv->getHeader(), $table['columns']);
		$this->assertEmpty($table['indexedColumns']);
		$this->assertNotEquals('0000-00-00 00:00:00', $table['created']);
		$this->assertEquals(count($this->_readCsv($expectationFile)) - 1, $table['rowsCount']);
		$this->assertNotEmpty($table['dataSizeBytes']);

		$this->assertLinesEqualsSorted(
			file_get_contents($expectationFile),
			$this->_client->exportTable($tableId), 'initial data imported into table'
		);
	}

	public function tableCreateData()
	{
		return array(
			array(__DIR__ . '/../_data/languages.csv', __DIR__ . '/../_data/languages.csv', false, self::BACKEND_REDSHIFT),
			array(__DIR__ . '/../_data/languages.csv', __DIR__ . '/../_data/languages.csv', false, self::BACKEND_MYSQL),
			array('https://s3.amazonaws.com/keboola-tests/languages.csv', __DIR__ . '/../_data/languages.csv', false, self::BACKEND_MYSQL),
			array(__DIR__ . '/../_data/languages.csv.gz', __DIR__ . '/../_data/languages.csv', false, self::BACKEND_MYSQL),
			array(__DIR__ . '/../_data/languages.csv.gz', __DIR__ . '/../_data/languages.csv', true, self::BACKEND_MYSQL),
			array(__DIR__ . '/../_data/languages.csv.gz', __DIR__ . '/../_data/languages.csv', true, self::BACKEND_REDSHIFT),
			array(__DIR__ . '/../_data/languages.csv', __DIR__ . '/../_data/languages.csv', true, self::BACKEND_MYSQL),
			array(__DIR__ . '/../_data/languages.csv', __DIR__ . '/../_data/languages.csv', true, self::BACKEND_REDSHIFT),
			array(__DIR__ . '/../_data/languages.camel-case-columns.csv', __DIR__ . '/../_data/languages.camel-case-columns.csv', true, self::BACKEND_MYSQL),
			array(__DIR__ . '/../_data/languages.camel-case-columns.csv', __DIR__ . '/../_data/languages.camel-case-columns.csv', true, self::BACKEND_REDSHIFT),
			array(__DIR__ . '/../_data/languages.camel-case-columns.csv', __DIR__ . '/../_data/languages.camel-case-columns.csv', false, self::BACKEND_REDSHIFT),
			array(__DIR__ . '/../_data/languages.camel-case-columns.csv', __DIR__ . '/../_data/languages.camel-case-columns.csv', false, self::BACKEND_MYSQL),
		);
	}

	public function testTableWithUnsupportedCharactersInNameShouldNotBeCreated()
	{
		try {
			$tableId = $this->_client->createTable(
				$this->getTestBucketId(),
				'languages.main',
				new CsvFile(__DIR__ . '/../_data/languages.csv')
			);
			$this->fail('Table with dot in name should not be created');
		} catch (\Keboola\StorageApi\ClientException $e) {
			$this->assertEquals('storage.tables.validation', $e->getStringCode());
		}
	}

	/**
	 * @param $async
	 * @dataProvider tableColumnSanitizeData
	 */
	public function testTableColumnNamesSanitize($backend, $async)
	{
		$csv = new Keboola\Csv\CsvFile(__DIR__ . '/../_data/filtering.csv');

		$method = $async ? 'createTableAsync' : 'createTable';
		$tableId = $this->_client->{$method}(
			$this->getTestBucketId(self::STAGE_IN, $backend),
			'sanitize',
			$csv
		);

		$table = $this->_client->getTable($tableId);
		$this->assertEquals(array('with_spaces', 'scrscz', 'with_underscore'), $table['columns']);
		$writeMethod = $async ? 'writeTableAsync' : 'writeTable';
		$this->_client->{$writeMethod}($tableId, new Keboola\Csv\CsvFile(__DIR__ . '/../_data/filtering.csv'));
	}

	public function tableColumnSanitizeData()
	{
		return $this->dataWithBackendPrepended(array(
			array(false),
			array(true)
		));
	}

	public function testTableCreateWithPK()
	{
		$tableId = $this->_client->createTable(
			$this->getTestBucketId(),
			'languages',
			new CsvFile(__DIR__ . '/../_data/languages.csv'),
			array(
				'primaryKey' => 'id',
			)
		);

		$table = $this->_client->getTable($tableId);
		$this->assertEquals(array('id'), $table['primaryKey']);
		$this->assertEquals(array('id'), $table['indexedColumns']);
	}

}