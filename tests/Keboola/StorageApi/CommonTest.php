<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martinhalamicek
 * Date: 22/05/14
 * Time: 16:35
 * To change this template use File | Settings | File Templates.
 */

class Keboola_StorageApi_CommonTest extends StorageApiTestCase
{

	public function testParseCsv()
	{
		$csvData = '"column1","column2"' . PHP_EOL
			. '"valu\ "",e1","value2"' . PHP_EOL
			. '"new'  . PHP_EOL . 'line","col2"'
		;

		$expectedSimple = array(
			array(
				"column1",
				"column2",
			),
			array(
				'valu\ ",e1', 'value2',
			),
			array(
				"new\nline","col2",
			),
		);
		$expectedHashmap = array(
			array(
				"column1" => 'valu\ ",e1',
				"column2" => 'value2',
			),
			array(
				"column1" => "new\nline",
				"column2" => "col2",
			),
		);


		$data = \Keboola\StorageApi\Client::parseCsv($csvData, false);
		$this->assertEquals($expectedSimple, $data, "Csv parse to flat array");

		$data = \Keboola\StorageApi\Client::parseCsv($csvData, true);
		$this->assertEquals($expectedHashmap, $data, "Csv parse to associative array");
	}

}