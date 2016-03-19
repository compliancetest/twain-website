define({ "api": [
  {
    "type": "get",
    "url": "testcase/:id",
    "title": "",
    "description": "<p>Method used to get Execution Profile data</p>",
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "404",
            "description": "<p>Test Case not found</p>"
          },
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "400",
            "description": "<p>product_id field is required</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\"message\":\"Test Case not found\",\"status_code\":404}",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": "{\"message\":\"product_id field is required\",\"status_code\":400}",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": "{\"message\":\"Test Case don't have test execution profile\",\"status_code\":404}",
          "type": "json"
        }
      ]
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "Parameter",
            "description": "<p>{Number} product_id Mandatory product_id value.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{ \"product_id\": 125 }",
          "type": "json"
        }
      ]
    },
    "permission": [
      {
        "name": "user"
      }
    ],
    "sampleRequest": [
      {
        "url": "http://hostname/api/testcases/123"
      }
    ],
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\"data\":{\"id\":21,\"type_name\":\"TCEF v1.1\",\"profile_name\":\"CAP-01a_v1.0 TEFC v1.0\",\"profile_description\":\"Test Case Execution Flow for CAP-01a test case\",\"purpose\":\"TCEF for Application test case\",\"token\":\"ef77b24465e975582c65b93c69326c70134bf0e0\",\"content\":{\"Profile\":{\"Type\":\"TCEF\",\"Purpose\":\"TCEF for Application test case\",\"Title\":\"CAP-01a_v1.0 TEFC\",\"Description\":\"Test Case Execution Flow for CAP-01a test case\",\"Version\":{\"Major\":1,\"Minor\":0}},\"Meta\":{\"SystemUnderTest\":\"Application\",\"Capabilities\":[{\"Cap\":\"CAP_SUPPORTEDCAPS\"}],\"InitialState\":4},\"TestSteps\":[[{\"Optional\":false,\"Triplet\":{\"From\":\"APP\",\"To\":\"DS\",\"DataGroup\":\"DG_CONTROL\",\"DataArgumentType\":\"DAT_USERINTERFACE\",\"Messages\":\"MSG_ENABLEDS\",\"pUserinterface\":{\"ShowUI\":true}},\"PassConditions\":[{\"ItemType\":\"ReturnCode\",\"Operator\":\"EQ\",\"Value\":\"TWRC_SUCCESS\",\"Step\":1}]}]]}}}",
          "type": "json"
        }
      ]
    },
    "version": "1.0.0",
    "filename": "app/Api/Controllers/TestCasesController.php",
    "group": "_var_www_website_laravel_app_Api_Controllers_TestCasesController_php",
    "groupTitle": "_var_www_website_laravel_app_Api_Controllers_TestCasesController_php",
    "name": "GetTestcaseId"
  }
] });
