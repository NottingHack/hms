<?php

require_once ('Setup/Common/SqlWriter.php');

class SqlWriterTest extends PHPUnit_Framework_TestCase {

	private $__writer;

	public function setUp() {
		$this->__writer = new SqlWriter();
	}

	public function testWrite_WhenPassedInNull_ReturnsSqlNull() {
		$result = $this->__writer->write(null);
		$this->assertEquals('NULL', $result);
	}

	public function testWrite_WhenPassedInPositiveInteger_ReturnsStringOfNumber() {
		$result = $this->__writer->write(2345436);
		$this->assertEquals('2345436', $result);
	}

	public function testWrite_WhenPassedInNumericZero_ReturnsStringZero() {
		$result = $this->__writer->write(0);
		$this->assertEquals('0', $result);
	}

	public function testWrite_WhenPassedInNegativeInteger_ReturnsStringOfNumber() {
		$result = $this->__writer->write(-253675);
		$this->assertEquals('-253675', $result);
	}

	public function testWrite_WhenPassedInPositiveDecimal_ReturnsStringOfNumber() {
		$result = $this->__writer->write(3.5);
		$this->assertEquals('3.5', $result);
	}

	public function testWrite_WhenPassedInDecimalZero_ReturnsStringOfNumber() {
		$result = $this->__writer->write(0.0);
		$this->assertEquals('0.0', $result);
	}

	public function testWrite_WhenPassedInNegativeDecimal_ReturnsStringOfNumber() {
		$result = $this->__writer->write(-85.64);
		$this->assertEquals('-85.64', $result);
	}

	public function testWrite_WhenPassedInEmptyString_ReturnsQuotedEmptyString() {
		$result = $this->__writer->write('');
		$this->assertEquals("''", $result);
	}

	public function testWrite_WhenPassedInStringWithNoApostrophes_ReturnsQuotedString() {
		$result = $this->__writer->write('the quick brown fox jumps over the lazy dog');
		$this->assertEquals("'the quick brown fox jumps over the lazy dog'", $result);
	}

	public function testWrite_WhenPassedInStringWithApostrophe_ReturnsEscapedQuotedString() {
		$result = $this->__writer->write("you don't want none of this");
		$this->assertEquals("'you don\'t want none of this'", $result);
	}

	public function testWriteInsert_WhenPassedInEmptyTableNameAndNullData_ReturnsNull() {
		$result = $this->__writer->writeInsert('', null);
		$this->assertEquals(null, $result);
	}

	public function testWriteInsert_WhenPassedInEmptyTableNameAndEmptyData_ReturnsNull() {
		$result = $this->__writer->writeInsert('', array());
		$this->assertEquals(null, $result);
	}

	public function testWriteInsert_WhenPassedInEmptyTableNameAndSingleRowOfData_ReturnsSqlForInsertingDataIntoUnnamedTable() {
		$result = $this->__writer->writeInsert('', array(
			array(
				'header1' => 'data1',
				'header2' => 2,
				'header3' => null,
			)
		));
		$expected = "INSERT INTO `` (`header1`, `header2`, `header3`) VALUES" . PHP_EOL . "('data1', 2, NULL);" . PHP_EOL;
		$this->assertEquals($expected, $result);
	}

	public function testWriteInsert_WhenPassedInEmptyTableNameAndMultipleRowsOfData_ReturnsSqlForInsertingDataIntoUnnamedTable() {
		$result = $this->__writer->writeInsert('', array(
			array(
				'header1' => 'data1',
				'header2' => 2,
				'header3' => null,
			),
			array(
				'header1' => 'foo',
				'header2' => -345,
				'header3' => 2.1,
			)
		));
		$expected = "INSERT INTO `` (`header1`, `header2`, `header3`) VALUES" . PHP_EOL . "('data1', 2, NULL)," . PHP_EOL . "('foo', -345, 2.1);" . PHP_EOL;
		$this->assertEquals($expected, $result);
	}

	public function testWriteInsert_WhenPassedInTableNameAndMultipleRowsOfData_ReturnsSqlForInsertingDataIntoUnnamedTable() {
		$result = $this->__writer->writeInsert('tableName', array(
			array(
				'header1' => 'data1',
				'header2' => 2,
				'header3' => null,
			),
			array(
				'header1' => 'foo',
				'header2' => -345,
				'header3' => 2.1,
			)
		));
		$expected = "INSERT INTO `tableName` (`header1`, `header2`, `header3`) VALUES" . PHP_EOL . "('data1', 2, NULL)," . PHP_EOL . "('foo', -345, 2.1);" . PHP_EOL;
		$this->assertEquals($expected, $result);
	}
}