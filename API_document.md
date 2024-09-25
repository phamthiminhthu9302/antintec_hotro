# Pre-requisites:
## Add these 2 headers into each API call 📞📞🤙:
Content-Type: application/vnd.api+json\
Accept: application/vnd.api+json\


### REGISTER
HTTP METHOD: POST\
URL: http://localhost:8000/api/register \
DATA TYPE: JSON

***BODY***
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

***RETURN***
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

***BODY***
<pre>
{
    "username":"test02@gmail.com":accept either username, email,phone,
    "password": "123456789"
}</pre>

***RETURN***
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
Authorization type: Bearer  ***(token value)*** 

***RETURN***
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

***BODY***
<pre>
{   "request_id" : 1,
    "payment_method" : "credit_card"
}</pre>

***RETURN***
<pre>
{
    "status": "SUCCESS",
    "data": 1,
    "message": "Operation successfully"
}</pre>
