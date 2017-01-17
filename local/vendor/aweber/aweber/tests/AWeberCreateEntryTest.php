<?php
require_once('aweber_api/aweber_api.php');
require_once('mock_adapter.php');

/**
 * Unit Tests for Creating Collection Entries
 * 
 * This class contains a set of unit tests verifying the
 * functionality related to creating an entry in a collection.
 */
class TestAWeberCreateEntry extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->adapter = get_mock_adapter();

        # Get CustomFields
        $url = '/accounts/1/lists/303449/custom_fields';
        $data = $this->adapter->request('GET', $url);
        $this->custom_fields = new AWeberCollection($data, $url, $this->adapter);

    }

    /**
     * Create Success
     * 
     * A unit test of a successful call to the Collection create method. 
     * Testing is limited to the Collection module; the OAuthAdapater 
     * module that handles the communications with the AWeber Public 
     * API Web Service is stubbed out.
     */
    public function testCreateSuccess() {

         $this->adapter->clearRequests();
         $resp = $this->custom_fields->create(array('name' => 'AwesomeField'));


         $this->assertEquals(sizeOf($this->adapter->requestsMade), 2);

         $req = $this->adapter->requestsMade[0];
         $this->assertEquals($req['method'], 'POST');
         $this->assertEquals($req['uri'], $this->custom_fields->url);
         $this->assertEquals($req['data'], array(
             'ws.op' => 'create',
             'name' => 'AwesomeField'));

         $req = $this->adapter->requestsMade[1];
         $this->assertEquals($req['method'], 'GET');
         $this->assertEquals($req['uri'], '/accounts/1/lists/303449/custom_fields/2');
     }
    
    /**
     * Create Success With Adapter
     * 
     * A unit test of a successful call to the Collection create method.
     * Testing covers calls to the OAuthAdapter module, though the actual call
     * to the Public API is mocked.
     * 
     * Verifies that a Custom Field with the specified name is returned in the
     * response.
     * 
     * Note! The actual Web Service responses contain additional headers and 
     * response attributes that are not currently relevant to these tests.
     */
    public function testCreateSuccessWithAdapter() {
        
        // Define the fake AWeber API responses.
        $getCollectionRsp = 
<<<EOT
{"total_size": 0, "start": 0, "entries": [], "resource_type_link" : "https://api.aweber.com/1.0/#custom_field-page-resource"}
EOT;

        $postCustomFieldRsp =
<<<EOT
HTTP/1.1 201 Created\r\nLocation: https://api.aweber.com/1.0/accounts/12345/lists/67890/custom_fields/1\r\n\r\n
EOT;

        $getCustomFieldRsp =
<<<EOT
HTTP/1.1 200 Ok\r\nDate: Mon, 30 Dec 2013 19:16:13 GMT\r\nContent-Type: application/json\r\nContent-Length: 225\r\n\r\n\r\n{"name": "Field With Spaces", "is_subscriber_updateable": false, "self_link": "https://api.aweber.com/1.0/accounts/12345/lists/67890/custom_fields/1", "resource_type_link": "https://api.aweber.com/1.0/#custom_field", "id": 1}
EOT;

                        
        // Create the AWeber object
        $consumerKey = "consumerkey";
        $consumerSecret = "consumersecret";
        $aweber = new AWeberAPI($consumerKey, $consumerSecret);

        // Set up the cURL Stub
        $stub = $this->getMock('CurlObject');
        $stub->expects($this->any())
             ->method('execute')
             ->will($this->onConsecutiveCalls($postCustomFieldRsp,
                                              $getCustomFieldRsp));
        $aweber->adapter->curl = $stub;

        // Create an empty custom field collection to work on.
        $url = "/accounts/12345/lists/67890/custom_fields";        
        $data = json_decode($getCollectionRsp, true);       
        $custom_fields = new AWeberCollection($data, $url, $aweber->adapter);
        
        // Finally the actual unit test. Create the new custom field
        $rsp = $custom_fields->create(array('name' => 'Field With Spaces'));
        $this->assertEquals($rsp->data['name'],'Field With Spaces');                        
    }

    /**
     * Create Failure With Adapter
     * 
     * A unit test of a failed call to the Collection create method.
     * Testing covers calls to the OAuthAdapter module, though the actual call
     * to the Public API is mocked.
     * 
     * Verifies that an AWeberAPIException is thrown as a result of a 
     * disallowed character.
     * 
     * Note! The actual Web Service responses contain additional headers and 
     * response attributes that are not currently relevant to these tests.
     */
    public function testCreateFailureWithAdapter() {
        
        // Define the fake AWeber API responses.
        $getCollectionRsp = 
<<<EOT
{"total_size": 0, "start": 0, "entries": [], "resource_type_link" : "https://api.aweber.com/1.0/#custom_field-page-resource"}
EOT;

        $postCustomFieldRsp =
<<<EOT
HTTP/1.1 400 Bad Request\r\nDate: Tue, 31 Dec 2013 15:01:40 GMT\r\nContent-Type: application/json\r\n\r\n{"error": {"status": 400, "documentation_url": "https://labs.aweber.com/docs/troubleshooting#badrequest", "message": "name: Invalid CustomField name specified", "type": "WebServiceError"}}
EOT;
                        
        // Create the AWeber object
        $consumerKey = "consumerkey";
        $consumerSecret = "consumersecret";
        $aweber = new AWeberAPI($consumerKey, $consumerSecret);

        // Set up the cURL Stub
        $stub = $this->getMock('CurlObject');
        $stub->expects($this->any())
             ->method('execute')
             ->will($this->returnValue($postCustomFieldRsp));
        $aweber->adapter->curl = $stub;

        // Create an empty custom field collection to work on.
        $url = "/accounts/12345/lists/67890/custom_fields";        
        $data = json_decode($getCollectionRsp, true);       
        $custom_fields = new AWeberCollection($data, $url, $aweber->adapter);
        
        // Create the new custom field
        try {
            $rsp = $custom_fields->create(array('name' => 'Field+With+Plus+Chars'));
        }
        
        // Finally the actual unit test. Verify that the create fails.
        catch (AWeberAPIException $expected){
            $this->assertEquals($expected->status, 400);
            return;
        }
        $this->fail('An AWeberResponseError was not raised');
    }
}
