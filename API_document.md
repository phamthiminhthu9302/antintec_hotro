# Pre-requisites:

## Reference: Take a glimpse at test folder for more information 🧐

## Add these 2 headers into each API call 📞📞🤙:

Content-Type: application/vnd.api+json\
Accept: application/vnd.api+json\

### Common Error Status Code

<pre>
- 422: Input Validation Failed (check input field again)
- 400: Bad Request
- 401: Unauthorized
- 404: Not Found
- 500: Internal Server Error (contact with BE 📞👴🏻)
</pre>

### REGISTER

HTTP METHOD: POST\
URL: http://localhost:8000/api/register \
DATA TYPE: JSON

**BODY**
<pre>
{
    "username": "test02",
    "email": "test02@gmail.com",
    "password": "12345678",
    "password_confirmation": "12345678",
    "phone": "012345678",
    "address": "192 Lầu 2 Huỳnh Mẫn Đạt",
    "role": "customer",
    "payment_method": "e_wallet"   
}</pre>

**RETURN**
<pre>
{
    "status": true,
    "message": "User create successfully",
    "token": "11|pJwnSo365L5n3CdGSLaJu3j2unINI3gKxqeATcgEaf0197b3"
}
</pre>

### LOGIN

HTTP METHOD: POST\
URL: http://localhost:8000/api/check-login \
DATA TYPE: JSON

**BODY**
<pre>
{
    "username":"test02@gmail.com":accept either username, email,phone,
    "password": "123456789"
}</pre>

**RETURN**
<pre>
{
    "status": true,
    "message": "User login successfully",
    "token": "13|tpwWcxbuSpZwbeKOdrcPpr8E4gmTsLjtF9N7wnqW352c7739"
}</pre>

### GET PROFILE

HTTP METHOD: GET\
URL: http://localhost:8000/api/profile \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)**

**RETURN**
<pre>
{
    "status": true,
    "message": "Profile Information",
    "data": {
        "user_id": 5,
        "username": "test02",
        "email": "test02@gmail.com",
        "phone": "012345678",
        "role": "customer",
        "address": "192 Lầu 2 Huỳnh Mẫn Đạt",
        "created_at": "2024-09-22T13:49:24.000000Z",
        "updated_at": "2024-09-22T13:49:24.000000Z",
        "payment_method": "e_wallet"
    }
</pre>

### UPDATE PROFILE

HTTP METHOD: PUT\
URL: http://localhost:8000/api/profile \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)**

**BODY**
   <pre>
     {
        "phone": "string",
        "address": "string",
        "role": "enum" customer or technician,
        "payment_method": "enum" e_wallet, cash, credit_card
     }
    </pre>
**RETURN**
<pre>
    "status": "SUCCESS",
    "data": {
        "user_id": 3,
        "username": "test02",
        "email": "test02@gmail.com",
        "phone": "000000000",
        "role": "technician",
        "address": "An Dương Vương, Q5",
        "created_at": "2024-09-22T05:47:03.000000Z",
        "updated_at": "2024-09-22T08:32:42.000000Z",
        "payment_method": "e_wallet"
    },
    "message": "Operation successfully"
</pre>

### CHANGE PAYMENT METHOD

HTTP METHOD: PUT\
URL: http://localhost:8000/api/profile/payment \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)**

**BODY**
<pre>
{   "request_id" : 1,
    "payment_method" : "credit_card"
}</pre>

**RETURN**
<pre>
{
    "status": "SUCCESS",
    "data": 1,
    "message": "Operation successfully"
}</pre>

## Task no.5

### GET MESSAGE ORDER BY CREATED_AT DESC

HTTP METHOD: GET\
URL: http://localhost:8000/api/messages \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)**

**BODY**
<pre>
{   "receiver_id" : "8"
}</pre>

**RETURN**
<pre>
{
    {"status":"SUCCESS","data":[
        {"message_id":17,"sender_id":12,"receiver_id":8,"request_id":14,"message":"sender id 12 send from php unit test to receiver id 8","is_seen":0,"created_at":"2024-10-24T03:17:14.000000Z","updated_at":"2024-10-24T03:17:14.000000Z"},
        {"message_id":15,"sender_id":12,"receiver_id":8,"request_id":13,"message":"test message 2 from user 12 send to user 8","is_seen":1,"created_at":"2024-09-24T02:35:19.000000Z","updated_at":"2024-09-24T02:35:19.000000Z"},
        {"message_id":16,"sender_id":12,"receiver_id":8,"request_id":13,"message":"test message 1 from user 12 send to user 8","is_seen":1,"created_at":"2024-09-24T02:15:19.000000Z","updated_at":"2024-09-24T02:15:19.000000Z"}
    ]
    ,"message":"Operation successfully"}
}</pre>

### SEND MESSAGE

HTTP METHOD: POST\
URL: http://localhost:8000/api/messages \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)**

**BODY**
<pre>
{   
    "request_id" => "14",
    "message" => "sender id 12 send from php unit test to receiver id 8",
    "receiver_id" => "8"
}</pre>

**RETURN**
<pre>
{
    "status":"SUCCESS",
    "data": {
        "sender_id":12,
        "receiver_id":"8",
        "message":"sender id 12 send from php unit test 2nd time to receiver id 8",
        "is_seen":false,"request_id":"14",
        "updated_at":"2024-10-24T03:34:08.000000Z",
        "created_at":"2024-10-24T03:34:08.000000Z",
        "message_id": "18"
    },
    "message":"Operation successfully"}
</pre>

### CREATE REQUEST

HTTP METHOD: POST\
URL: http://localhost:8000/api/requests \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)**
**Bearer token is customer**

**BODY**
<pre>
{   
    "technician_id" => "8",
    "service_id" => "1",
    "latitude" => "100",
    "longitude" => "200",
    "photo" => null,
    "description" => "xe hư mất rồi",
    "status" => "in_progress",
    "location" => "192 lầu 2 huỳnh mẫn đạt",
    "requested_at" => date('Y-m-d h:i:s'),
}</pre>

**RETURN**
Send back Notification information
<pre>
    {
        "status":"SUCCESS",
        "data":"Yêu cẩu Vá xe lưu động của bạn đang được thực hiện",
        "message":"Operation successfully"
    }
</pre>

### UPDATE REQUEST STATUS

HTTP METHOD: PUT\
URL: http://localhost:8000/api/requests \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)** \
**Bearer token need to be an appropriate technician id in that request**

**BODY**
<pre>
{   
    "request_id" => "15",
    "status" => "cancelled"
}</pre>

**RETURN**
Send back Notification information to user with appropriate customer_id from the request
<pre>
    {
        "status":"SUCCESS",
        "data":"Yêu cẩu Vá xe lưu động của bạn đã bị hủy",
        "message":"Operation successfully"
    }
</pre>

### CUSTOMER READ NOTIFICATION

HTTP METHOD: PUT\
URL: http://localhost:8000/api/notification/read \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)** \

**BODY**
<pre>
{   
    "notification_id" => "15",
}</pre>

**RETURN**
Send back Notification information to user with appropriate customer_id from the request
<pre>
    {
        "status":"SUCCESS",
        "data": {
            "notification_id":22,
            "user_id":12,
            "message":"Yêu cẩu Vá xe lưu động của bạn đang được thực hiện",
            "is_read":true,
            "created_at":"2024-10-24T04:43:30.000000Z",
            "updated_at":"2024-10-24T05:21:28.000000Z"
        },
        "message":"Operation successfully"
    }
</pre>

## TASK No.7

### HTTP METHOD: GET\

URL: http://localhost:8000/api/services-management \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)**

**BODY**
<pre>
{   
  
}</pre>

**RETURN**
Send back Notification information to user with appropriate customer_id from the request
<pre>
  {
    "status":"SUCCESS",
    "data":[{
        "request_id":15,
        "customer_id":12,
        "technician_id":8,
        "service_id":1,
        "latitude":"100.000000",
        "longitude":"200.000000",
        "photo":null,
        "description":"xe hư mất rồi",
        "status":"in_progress",
        "location":"192 lầu 2 huỳnh mẫn đạt",
        "requested_at":"2024-10-24 11:00:41",
        "completed_at":null,
        "created_at":"2024-10-24T04:00:41.000000Z",
        "updated_at":"2024-10-24T04:42:35.000000Z",
        "service": {
            "service_id":1,"name":"Vá xe lưu động",
            "description":"vá ruột xe\/lốp xe ga (cứu hộ xe máy)",
            "price":"90000.00",
            "created_at":null,
            "updated_at":null
            }
        }
    .../omitted data similar like above
</pre>

## TASK NO.8

### GET TECHNICIAN SERVICE

HTTP METHOD: GET\
URL: http://localhost:8000/api/technician/services \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)** \

**BODY**
<pre>
{   
}</pre>

**RETURN**
Send back Notification information to user with appropriate customer_id from the request
<pre>
    {
        "status":"SUCCESS",
        "data":[{
            "technician_id":18,
            "service_id":1,
            "created_at":"2024-09-24T01:15:19.000000Z",
            "updated_at":"2024-09-24T01:15:19.000000Z",
            "status":"active",
            "available_from":"2024-09-24 08:15:19",
            "available_to":"2024-09-24 10:15:19"
            },
            {
            "technician_id":18,
            "service_id":2,
            "created_at":"2024-09-24T01:15:19.000000Z",
            "updated_at":"2024-09-24T01:15:19.000000Z",
            "status":"inactive",
            "available_from":"2024-09-24 08:15:19",
            "available_to":"2024-09-24 13:15:19"
            },
            {
            "technician_id":18,
            "service_id":3,
            "created_at":"2024-10-23T10:13:58.000000Z",
            "updated_at":"2024-10-23T10:13:58.000000Z",
            "status":"active",
            "available_from":"2024-10-23 17:49:59",
            "available_to":"2024-10-23 17:13:58"
        }],
        "message":"Operation successfully"}
</pre>

### CREATE TECHNICIAN SERVICE

HTTP METHOD: POST\
URL: http://localhost:8000/api/technician/services \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)** \

**BODY**
<pre>
{   
    'service_id' => '5',
    'status' => 'active',
    'available_from' => date('Y-m-d H:i:s'),
    'available_to' => date('Y-m-d H:i:s'),
}</pre>

**RETURN**
<pre>
    {
        "status":"SUCCESS",
        "data":{
            "technician_id":12,
            "service_id":"5",
            "status":"active",
            "available_from":"2024-10-24 15:47:32",
            "available_to":"2024-10-24 15:47:32",
            "updated_at":"2024-10-24T08:47:32.000000Z",
            "created_at":"2024-10-24T08:47:32.000000Z",
        },
        "message":"Operation successfully"
    }
</pre>

### TECHNICIAN CAN PUT THE SERVICE ACTIVE OR INACTIVE

HTTP METHOD: PUT\
URL: http://localhost:8000/api/technician/services \
DATA TYPE: JSON\
Authorization type: Bearer  **(token value)** 

**BODY**
**not required all fields**
<pre>
{    'service_id' => '5', //required
     'status' => 'inactive', //optional
     'available_from' => date('Y-m-d H:i:s'), //optional
     'available_to' => date('Y-m-d H:i:s'), //optional
}</pre>

**RETURN**
Send back Notification information to user with appropriate customer_id from the request
<pre>
   {
        "status":"SUCCESS",
        "data":{
            "technician_id":12,
            "service_id":"5",
            "created_at":"2024-10-24T08:47:32.000000Z",
            "updated_at":"2024-10-24T08:47:32.000000Z",
            "status":"inactive",
            "available_from":"2024-10-24 15:51:33",
            "available_to":"2024-10-24 15:47:32"
        },
        "message":"Operation successfully"
   }
</pre>
