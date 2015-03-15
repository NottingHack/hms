<?php
    App::uses('BankTransactionsController', 'Controller');
    App::uses('BankTrancaction', 'Model');
    
    App::uses('MembersController', 'Controller');
    
    
    App::build(array('TestCase' => array('%s' . 'Test' . DS . 'Case' . DS . 'Model' . DS)), App::REGISTER);
    App::uses('EmailRecordTest', 'TestCase');
    
    App::build(array('TestController' => array('%s' . 'Test' . DS . 'Lib' . DS)), App::REGISTER);
    App::uses('HmsControllerTestBase', 'TestController');
    
    App::uses('PhpReader', 'Configure');
    Configure::config('default', new PhpReader());
    Configure::load('hms', 'default');
    
    class BankTransactionsControllerTest extends HmsControllerTestBase
    {
        public $fixtures = array( 'app.Member', 'app.Status', 'app.Group', 'app.GroupsMember', 'app.Account', 'app.Pin', 'app.StatusUpdate', 'app.ForgotPassword', 'app.MailingLists', 'app.MailingListSubscriptions', 'app.EmailRecord' );
        
        public function setUp()
        {
            parent::setUp();
            
            $this->BankTransactionsController = new BankTransactionsController();
            $this->BankTransactionsController->constructClasses();
        }
 
        public function testUploadCsvInvalidFile()
		{
			$contents = 'iVBORw0KGgoAAAANSUhEUgAAAC0AAAAtCAYAAAA6GuKaAAAACXBIWXMAAAsTAAALEwEAmpwYAAAKT2lDQ1BQaG90b3Nob3AgSUNDIHByb2ZpbGUAAHjanVNnVFPpFj333vRCS4iAlEtvUhUIIFJCi4AUkSYqIQkQSoghodkVUcERRUUEG8igiAOOjoCM';
			$guid = null;

			$this->_setupTestUploadCsv();

			$this->controller->Session->expects($this->once())->method('setFlash')->with('That did not seem to be a valid bank .csv file');
			$this->controller->Nav->expects($this->never())->method('add');

			$this->_runTestUploadCsv($contents, $guid);

			$this->assertArrayHasKey('Location', $this->headers);
			$this->assertContains('/banktransactions/uploadCsv', $this->headers['Location']);
		}

		public function testUploadCsvDudFile()
		{
			$contents = 'This, is not a valid .csv file, even though, it has, the correct, number, of commas';
			$guid = null;
			$this->_setupTestUploadCsv();
			$this->controller->Session->expects($this->once())->method('setFlash')->with('That did not seem to be a valid bank .csv file');
			$this->controller->Nav->expects($this->never())->method('add');
			$this->_runTestUploadCsv($contents, $guid);

			$this->assertArrayHasKey('Location', $this->headers);
			$this->assertContains('/banktransactions/uploadCsv', $this->headers['Location']);
		}

		public function testUploadCsvValidFileNoMembers()
		{
			$contents =
			'Date, Type, Description, Value, Balance, Account Name, Account Number
			,,,,,,
			,,,,,,
			,,,,,,
			06/02/2013,BAC,"\'A NAME , HSNOTTSVD74BY3C8 , FP 06/02/13 0138 , 300000000062834772",15,1664.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			06/02/2013,BAC,"\'DOROTHY D D/2011 , DOROTHY DEVAL",15,1679.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			06/02/2013,BAC,"\'SIMPMSON T , HSNOTTSTYX339RW3",10,1689.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			07/02/2013,BAC,"\'C DAVIES , CHRIS , FP 07/02/13 0034 , 00156265632BBBVSCR",5,1694.08,\'NOTTINGHACK,\'558899-45687951';
			$guid = null;
			$this->_setupTestUploadCsv();

			$this->controller->Session->expects($this->once())->method('setFlash')->with('No new member payments in .csv.');
			$this->controller->Nav->expects($this->never())->method('add');

			$this->_runTestUploadCsv($contents, $guid);

			$this->assertArrayHasKey('Location', $this->headers);
			$this->assertContains('/members', $this->headers['Location']);
		}

		public function testUploadCsvDudGuid()
		{
			$contents = null;
			$guid = '123456789';
			$this->_setupTestUploadCsv();
			$this->controller->Session->expects($this->never())->method('setFlash');
			$this->controller->Nav->expects($this->never())->method('add');
			$this->_runTestUploadCsv($contents, $guid);
		}

		public function testUploadValidFile()
		{
			$contents = 
			'Date, Type, Description, Value, Balance, Account Name, Account Number
			,,,,,,
			,,,,,,
			,,,,,,
			06/02/2013,BAC,"\'A NAME , HSNOTTSFGXWGKF48 , FP 06/02/13 0138 , 300000000062834772",15,1664.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			06/02/2013,BAC,"\'DOROTHY D D/2011 , DOROTHY DEVAL",15,1679.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			24/02/2013,BAC,"\'SIMPMSON T , HSNOTTSHVQGT3XF2",10,1689.08,\'NOTTINGHACK,\'558899-45687951
			,,,,,,
			07/02/2013,BAC,"\'C DAVIES , CHRIS , FP 07/02/13 0034 , 00156265632BBBVSCR",5,1694.08,\'NOTTINGHACK,\'558899-45687951';
			$guid = null;

			$generatedGuid = String::uuid();
			$this->_setupTestUploadCsv();
			$this->controller->Session->expects($this->never())->method('setFlash');
			$this->controller->Nav->expects($this->once())->method('add')->with('Approve All', 'banktransactions', 'uploadCsv', array($generatedGuid), 'positive');
			$this->controller->expects($this->once())->method('getMemberIdSessionKey')->will($this->returnValue($generatedGuid));

			$this->_runTestUploadCsv($contents, $guid);

			$this->_setupTestUploadCsv();
			$this->controller->Auth->staticExpects($this->any())->method('user')->will($this->returnValue(5));
			$this->controller->Session->expects($this->once())->method('setFlash')->with('Successfully approved member Ryan Miles\nSuccessfully approved member Evan Atkinson\n');
			$this->controller->Nav->expects($this->never())->method('add');

			// Email stuff
			$this->controller->email->expects($this->exactly(4))->method('config');
			$this->controller->email->expects($this->exactly(4))->method('from');
			$this->controller->email->expects($this->exactly(4))->method('sender');
			$this->controller->email->expects($this->exactly(4))->method('emailFormat');
			$this->controller->email->expects($this->exactly(4))->method('to');
			$this->controller->email->expects($this->exactly(4))->method('subject');
			$this->controller->email->expects($this->exactly(4))->method('template');
			$this->controller->email->expects($this->exactly(4))->method('viewVars');
			$this->controller->email->expects($this->exactly(4))->method('send')->will($this->returnValue(true));
			
			$this->_runTestUploadCsv($contents, $generatedGuid);

			$this->assertEqual($this->controller->Member->getStatusForMember(13), Status::CURRENT_MEMBER);
			$this->assertEqual($this->controller->Member->getStatusForMember(14), Status::CURRENT_MEMBER);
		}

		private function _setupTestUploadCsv()
		{
			$this->controller = $this->generate('BankTransactions', array(
				'components' => array(
					'Auth' => array(
						'user',
					),
					'Session' => array(
						'setFlash',
					),
					'Nav' => array(
						'add',
					),
				),
				'methods' => array(
					'getMemberIdSessionKey',
				),
			));

			// prevents 'You are not authorized to access that location' message from being set as a flash message which
			// seems to break some Controller tests since upgrading from CakekPHP 2.3.5 to 2.6.2
			$this->controller->Auth->authError = false;

			$mockEmail = $this->getMock('CakeEmail');
			$this->controller->email = $mockEmail;

//			$this->controller->BankTransactions->setDataSource('test');
//			$this->controller->BankTransactions->Account->setDataSource('test');
		}

		private function _runTestUploadCsv($fileContents, $guid)
		{
			$action = 'banktransactions/uploadCsv';
			if($guid != null)
			{
				$action .= '/' . $guid;
			}

			if($fileContents == null)
			{
				$this->testAction($action);
			}
			else
			{
				$data = $this->_makeFileUploadData($fileContents);
				$this->testAction($action, array('data' => $data, 'method' => 'post'));
			}
		}

		private function _makeFileUploadData($contents)
		{
			$filePath = $this->_makeTmpFile($contents);
			if($filePath != false)
			{
				return array(
					'FileUpload' => array(
						'filename' => array(
							'name' => 'uploaded.tmp',
							'type' => $this->_getFileType($filePath),
							'tmp_name' => $filePath,
							'error' => 0,
							'size' => filesize($filePath),
						),
					),
				);
			}

			return false;
		}

		private function _getFileType($filename)
		{
			// None of the proper ways to do this seem to work, but it doesn't matter for our tests
			return 'text';
		}

		private function _makeTmpFile($contents)
		{
			$tmpFile = tempnam(sys_get_temp_dir(), 'tst');
			if($tmpFile != false)
			{
				$filehandle = fopen($tmpFile, 'w');
				if($filehandle != false)
				{
					$success = fwrite($filehandle, $contents);
					fclose($filehandle);
					if($success)
					{
						return $tmpFile;
					}
				}
			}

			return false;
		}
    }
?>