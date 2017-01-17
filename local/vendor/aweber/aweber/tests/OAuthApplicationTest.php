<?php
require_once('aweber_api/aweber_api.php');
require_once('aweber_api/curl_object.php');
require_once('mock_adapter.php');

if (!class_exists('Object')) {
    class Object {}
}

class PatchedOAuthApplication extends OAuthApplication {
    
    public $signatureBase = false;    
    
    public function createSignature($sigBase, $sigKey) {
        $this->signatureBase = $sigBase;
        switch ($this->signatureMethod) {
        case 'HMAC-SHA1':
        default:
            return base64_encode(hash_hmac('sha1', $sigBase, $sigKey, true));
        }        
    }    
}

class TestOAuthApplication extends PHPUnit_Framework_TestCase {

    public $stubrsp = 
        "HTTP/1.1 200 Ok\r\nDate: Fri, 20 Dec 2013 21:23:38 GMT\r\nContent-Type: application/json\r\n\r\n{data:fake}";	

    public function setUp() {
        $parentApp = false;
        $this->oauth = new OAuthApplication($parentApp);
        $this->oauth->consumerSecret = 'CONSUMERSECRET';
        $this->oauth->consumerKey = 'consumer_key';
    }

    /**
     * testUniqueNonce
     *
     * GenerateNonce should generate a unique string
     * @access public
     * @return void
     */
    public function testUniqueNonce() {
        $values = array();
        foreach (range(1,100) as $i) {
            $val = $this->oauth->generateNonce();
            $this->assertFalse(in_array($val, $values), 'Generated nonce should be unique');
            $values[] = $val;
        }
    }

    public function testAddGetParams() {
        $url = 'http://www.sometestsite.com/';
        $data = array(
            'keyA' => 'Some Value',
            'keyC' => 'some other value',
            'keyB' => 'yet another value',
        );

        $this->assertEquals(
            'keyA=Some%20Value&keyB=yet%20another%20value&keyC=some%20other%20value',
            $this->oauth->buildData($data));
    }

    /**
     * testUniqueNonceSameTime
     *
     * GenerateNonce should generate unique strings, even with the same timestamp
     * @access public
     * @return void
     */
    public function testUniqueNonceSameTime() {
        $time = time();
        $values = array();
        foreach (range(1,100) as $i) {
            $val = $this->oauth->generateNonce($time);
            $this->assertFalse(in_array($val, $values), 'Generated nonce should be unique,'.
                        ' even with identical timestamp');
            $values[] = $val;
        }
    }

    /**
     * generateTimestamp
     *
     * Ensure generateTimestamp returns a time in epoch seconds.
     * @access public
     * @return void
     */
    public function testGenerateTimestamp() {
        $time = $this->oauth->generateTimestamp();
        $this->assertTrue(is_int($time), 'Timestamp must be an integer');
        $this->assertTrue($time > 0, 'Timestamp must be positive.');
        $this->assertTrue($this->oauth->generateTimestamp() >= $time,
            'Multiple calls to generateTimestamp should always be equal or greater.');
    }

    /**
     * testCreateSignature
     *
     * Test that a new signature is generated based on the data
     * @access public
     * @return void
     */
    public function testCreateSignature() {
        $sigBase = '342refd435gdfxw354xfbw364fdg'; // Random string
        $sigKey  = 'gdgdfet4gdffgd4etgr'; // Random string as well
        $signature = $this->oauth->createSignature($sigBase, $sigKey);
        $this->assertNotEmpty($signature, 'Returns a valid signature');
        $this->assertTrue(strpos($signature, $sigBase) === false, 'Signature does not contain base');
        $this->assertTrue(strpos($signature, $sigKey) === false, 'Signature does not contain key');
    }

    /**
     * testCreateSignatureUniqueness
     *
     * Verify that signatures are unique
     * @access public
     * @return void
     */
    public function testCreateSignatureUniqueness() {
        $sigBase = '342refd435gdfxw354xfbw364fdg'; // Random string
        $sigKey  = 'gdgdfet4gdffgd4etgr'; // Random string as well
        $signature  = $this->oauth->createSignature($sigBase, $sigKey);
        $signature2 = $this->oauth->createSignature($sigBase, $sigKey);
        $this->assertEquals($signature, $signature2, 'Signatures with same parameters are identical.');
        $sigKey = $sigKey.'1';

        $sig3 = $this->oauth->createSignature($sigBase, $sigKey);
        $this->assertNotEquals($signature, $sig3, 'Changing key creates different signature');
    }


    /**
     * testGetVersion
     *
     * Tests that the default OAuth version is currently 1.0
     * @access public
     * @return void
     */
    public function testGetVersion() {
        $version = $this->oauth->version;
        $this->assertEquals($version, '1.0', 'Default version is 1.0');
    }

    /**
     * testOAuthUser
     *
     * Tests the the OAuthUser class exists and has all its necessary data
     * @access public
     * @return void
     */
    public function testOAuthUser() {
        $user = new OAuthUser();

        $this->assertFalse($user->requestToken);
        $this->assertFalse($user->tokenSecret);
        $this->assertFalse($user->authorizedToken);
        $this->assertFalse($user->accessToken);
    }

    /**
     * generateOAuthUser
     *
     * Generate a mock OAuth user
     *
     * @access protected
     * @return void
     */
    protected function generateOAuthUser() {
        $data = array(
            'token'    => 'authorized token',
            'secret'   => 'abcdefg',
        );

        $user = new OAuthUser();
        $user->accessToken = $data['token'];
        $user->tokenSecret = $data['secret'];

        return array($user, $data);
    }

    /**
     * testCreatSignatureKey
     *
     * Test that signature key is generated correctly
     * @access public
     * @return void
     */
    public function testCreatSignatureKey() {
        list($user, $data) = $this->generateOAuthUser();
        $this->oauth->user = $user;

        $sigKey = $this->oauth->createSignatureKey();
        $this->assertEquals('CONSUMERSECRET&abcdefg', $sigKey); //, 'Signature key generated matches');
    }

    /**
     * testGetOAuthRequestData
     *
     * @access public
     * @return void
     */
    public function testGetOAuthRequestData() {
        $this->oauth->user = new OAuthUser();
        $data = $this->oauth->getOAuthRequestData();
        $tempData =  array(
            'oauth_consumer_key' => 'consumer_key',
            'oauth_token' =>  '',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0');

        // Check that timestamp and nonce are set.
        $this->assertTrue(!empty($data['oauth_timestamp']));
        $this->assertTrue(!empty($data['oauth_nonce']));

        // Remove those two items, since they are always unique
        unset($data['oauth_timestamp']);
        unset($data['oauth_nonce']);

        ksort($data);
        ksort($tempData);

        $this->assertSame($data, $tempData, 'Aside from timestamp and nonce, the rest should be identical');
    }

    public function generateRequestData() {
        list($user, $data) = $this->generateOAuthUser();
        $this->oauth->user = $user;

        $requestData = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3');

        $mergeData = $this->oauth->mergeOAuthData($requestData);
        return array($mergeData, $requestData);
    }

    public function testMergeOAuthData() {
        list($mergeData, $requestData) = $this->generateRequestData();
        $this->assertEquals($mergeData['key1'], $requestData['key1']);
        $this->assertEquals($mergeData['oauth_consumer_key'], $this->oauth->consumerKey);
    }

    public function testCreateSignatureBase() {
        list($mergeData, $requestData) = $this->generateRequestData();
        $method = 'GET';
        $url = 'http://www.someservice.com/chicken-nuggets';

        $baseString = $this->oauth->createSignatureBase($method, $url, $mergeData);
        $this->assertNotEmpty($baseString);
        $this->assertTrue(strpos($baseString, $method) !== false);
        $this->assertTrue(strpos($baseString, urlencode($url))!== false);
    }
	
    /**
     * testCreateSignatureBaseEscapeParamWithPlus
     * 
     * Test that reserved characters escaped in the URL query params are preserved
     * correctly in the signature base. In this case, a %2B (plus) should be
     * converted into a %252B in the signature base.
     *
     * @access public
     * @return void
     */
    public function testCreateSignatureBaseEscapeParamWithPlus() {
        list($mergeData, $requestData) = $this->generateRequestData();
        $method = 'GET';
        $url = 'http://www.somewhere.com/chicken?email=iluvchkn%2B10@somewhere.com';
        $encodedPlus = '%252B';

        $baseString = $this->oauth->createSignatureBase($method, $url, $mergeData);
        $this->assertTrue(strpos($baseString, $encodedPlus)!==FALSE);
    }
	
    public function testSignRequest() {
        list($data, $requestData) = $this->generateRequestData();
        $method = 'GET';
        $url = 'http://www.someservice.com/chicken-nuggets';

        $signedData = $this->oauth->signRequest($method, $url, $data);
        foreach ($data as $key => $val) {
            $this->assertEquals($signedData[$key], $val, 'Signed data has correct value for "'.$key.'"');
        }

        $this->assertTrue(!empty($signedData['oauth_signature']));
    }

    /**
     * testPrepareRequest
     *
     * @access public
     * @return void
     */
    public function testPrepareRequest() {
        list($data, $requestData) = $this->generateRequestData();
        $method = 'GET';
        $url = 'http://www.someservice.com/chicken-nuggets';

        $signedData = $this->oauth->prepareRequest($method, $url, $requestData);

        // Test that a nonce and timestamp was generated, then remove the one from our base data so that we
        // don't try to compare the two. They should still be different.
        $this->assertTrue(!empty($signedData['oauth_nonce']), 'Verify nonce was generated');
        $this->assertTrue(!empty($signedData['oauth_timestamp']), 'Verify nonce was generated');
        unset($data['oauth_nonce']);
        unset($data['oauth_timestamp']);

        foreach ($data as $key => $val) {
            $this->assertEquals($signedData[$key], $val, 'Signed data has correct value for "'.$key.'"');
        }

        $this->assertTrue(!empty($signedData['oauth_signature']));
    }

    /**
     * testParseResponse
     *
     * @access public
     * @return void
     */
    public function testParseResponse() {
        $response = new Object();
        $response->body = 'oauth_token=oTkBjHdPYyP7j13RffGpllNhktOR775h6jk48D1cu8Y&oauth_token_secret=GRRa1E7MMm526nql1hETKHMu2BvAXpvHaCu332TPAJ4&oauth_callback_confirmed=true';
        $data = $this->oauth->parseResponse($response);
        $dataShouldBe = array(
            'oauth_token' => 'oTkBjHdPYyP7j13RffGpllNhktOR775h6jk48D1cu8Y',
            'oauth_token_secret' => 'GRRa1E7MMm526nql1hETKHMu2BvAXpvHaCu332TPAJ4',
            'oauth_callback_confirmed' => 'true',
        );
        $this->assertSame($data, $dataShouldBe, 'Data is parsed correctly.');
    }

    public function testRequestReturnValueIsZeroNotInJSONFormat(){
        $this->adapter = get_mock_adapter();
        $url = '/accounts/1/lists/303449/subscribers?email=someone%40example.com&ws.show=total_size';
        $data = $this->adapter->request('GET', $url);
        $this->assertTrue(isset($data));
        $this->assertEquals($data,0);
    }

    public function testMakeRequestGet() {
        $stub = $this->getMock('CurlObject');
        $stub->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($this->stubrsp));		
        $this->oauth->curl = $stub;
        $rsp = $this->oauth->makeRequest("GET",
             'http://www.example.com/fakeresource');
        $this->assertEquals($rsp, "{data:fake}");	
    }

    public function testMakeRequestPost() {
        $stub = $this->getMock('CurlObject');
        $stub->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($this->stubrsp));		
        $this->oauth->curl = $stub;
        $rsp = $this->oauth->makeRequest("POST", 
            'http://www.example.com/fakeresource');
        $this->assertEquals($rsp, "{data:fake}");	
    }	

    public function testMakeRequestPut() {
        $stub = $this->getMock('CurlObject');
        $stub->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($this->stubrsp));		
        $this->oauth->curl = $stub;
        $rsp = $this->oauth->makeRequest("PATCH", 
            'http://www.example.com/fakeresource');
        $this->assertEquals($rsp, "{data:fake}");	
    }

    public function testMakeRequestDelete() {
        $stub = $this->getMock('CurlObject');
        $stub->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($this->stubrsp));		
        $this->oauth->curl = $stub;
        $rsp = $this->oauth->makeRequest("DELETE", 
            'http://www.example.com/fakeresource');
        $this->assertEquals($rsp, "{data:fake}");	
    }
    
    /**
     * testMakeRequestContainsReservedCharInUrl
     *
     * Test request behavior as it relates to reserved chars in URL.
     * 
     * This test verifies that escaped characters in the URL query
     * params are handled correctly when generating the oauth
     * signature base. For this specific test, an escaped plus
     * sign needs to show up as %252B in the signature, not as
     * %25252B
     * 
     * @access public
     * @return void
     */
    public function testMakeRequestContainsReservedCharInUrl() {
        $parentApp = false;
        $patchedoauth = new PatchedOAuthApplication($parentApp);
        $patchedoauth->consumerSecret = 'CONSUMERSECRET';
        $patchedoauth->consumerKey = 'consumer_key';
       
        $stub = $this->getMock('CurlObject');
        $stub->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($this->stubrsp));                    
        $patchedoauth->curl = $stub;
        $rsp = $patchedoauth->makeRequest("GET", 
            'http://www.example.com/fake?email=noone%2B@sp.com');
        $this->assertRegExp('/.+(\%252B).+/', $patchedoauth->signatureBase);
    }

    /**
     * testMakeRequestContainsSeperatorInUrl
     *
     * Test request behavior as it relates to separaters in URL.
     * 
     * This test verifies that a non-seperator equals sign in the URL 
     * query params is handled correctly when generating the oauth
     * signature base. For this specific test, an escaped equals
     * character needs to show up as %253D in the signature, not as
     * %25253D
     * 
     * @access public
     * @return void
     */    
    public function testMakeRequestContainsSeparatorInUrl() {
        $parentApp = false;
        $patchedoauth = new PatchedOAuthApplication($parentApp);
        $patchedoauth->consumerSecret = 'CONSUMERSECRET';
        $patchedoauth->consumerKey = 'consumer_key';
       
        $stub = $this->getMock('CurlObject');
        $stub->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($this->stubrsp));                    
        $patchedoauth->curl = $stub;
        $rsp = $patchedoauth->makeRequest("GET", 
            'http://www.example.com/fake?email=noone%3D@sp.com');
        $this->assertRegExp('/.+(\%253D).+/', $patchedoauth->signatureBase);
    }    

    /**
     * testMakeRequestContainsReservedCharInData
     *
     * Test request behavior as it relates to reserved chars in data.
     * 
     * This test verifies that reserved characters in the data array 
     * are handled correctly when generating the oauth signature base.
     * For this specific test, a plus sign needs to show up as %252B 
     * in the signature, not as + or %25252B
     * 
     * @access public
     * @return void
     */    
    public function testMakeRequestContainsReservedCharInData() {
        $parentApp = false;
        $patchedoauth = new PatchedOAuthApplication($parentApp);
        $patchedoauth->consumerSecret = 'CONSUMERSECRET';
        $patchedoauth->consumerKey = 'consumer_key';
       
        $stub = $this->getMock('CurlObject');
        $stub->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($this->stubrsp));                    
        $patchedoauth->curl = $stub;
        $rsp = $patchedoauth->makeRequest("GET", 'http://www.example.com/fake',
            array('email' => 'noone+1@sp.com'));
        $this->assertRegExp('/.+(\%252B).+/', $patchedoauth->signatureBase);
    }
    
    /**
     * testMakeRequestContainsSeperatorInData
     *
     * Test request behavior as it relates to separators in data array.
     * 
     * This test verifies that a non-separator equals sign in the data
     * array is handled correctly when generating the oauth signature base. 
     * For this specific test, an equals sign needs to show up as %253D 
     * in the signature, not as %25253D
     * 
     * @access public
     * @return void
     */    
    public function testMakeRequestContainsSeparatorInData() {
        $parentApp = false;
        $patchedoauth = new PatchedOAuthApplication($parentApp);
        $patchedoauth->consumerSecret = 'CONSUMERSECRET';
        $patchedoauth->consumerKey = 'consumer_key';
       
        $stub = $this->getMock('CurlObject');
        $stub->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($this->stubrsp));                    
        $patchedoauth->curl = $stub;
        $rsp = $patchedoauth->makeRequest("GET", 'http://www.example.com/fake',
            array('email' => 'noone=1@sp.com'));
        $this->assertRegExp('/.+(\%253D).+/', $patchedoauth->signatureBase);
    }    
}
