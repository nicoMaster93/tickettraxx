---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://concrete.test/docs/collection.json)

<!-- END_INFO -->

#v 1.0.0


<!-- START_4ff52ac647ee8805b2ed545f24ee1926 -->
## Login contractor
Generate an access token to use in the rest of the app

> Example request:

```bash
curl -X POST \
    "http://concrete.test/api/contractor/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"user":"nihil","pass":"esse"}'

```

```javascript
const url = new URL(
    "http://concrete.test/api/contractor/login"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "user": "nihil",
    "pass": "esse"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/contractor/login`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `user` | String |  required  | Contractor Email.
        `pass` | String |  required  | Contractor password.
    
<!-- END_4ff52ac647ee8805b2ed545f24ee1926 -->

<!-- START_19102cf11852351972166f484c88a45c -->
## Edit contractor
Update basic fields of contractor

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X PUT \
    "http://concrete.test/api/contractor/update" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"name":"molestiae","email":"ea","password":"aut","repeat_password":"numquam"}'

```

```javascript
const url = new URL(
    "http://concrete.test/api/contractor/update"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "molestiae",
    "email": "ea",
    "password": "aut",
    "repeat_password": "numquam"
}

fetch(url, {
    method: "PUT",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/contractor/update`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `name` | String |  required  | Contractor Name.
        `email` | String |  required  | Contractor Email.
        `password` | String |  optional  | User password.
        `repeat_password` | String |  optional  | User password again.
    
<!-- END_19102cf11852351972166f484c88a45c -->

<!-- START_bde14a442e2b858d84bb71da86fcc102 -->
## Logout contractor
Logout contractor

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X POST \
    "http://concrete.test/api/contractor/logout" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://concrete.test/api/contractor/logout"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/contractor/logout`


<!-- END_bde14a442e2b858d84bb71da86fcc102 -->

<!-- START_b5da8b29b47755a2eb3691637cc99826 -->
## Collection of tickets
Shows all tickets by contractor

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET \
    -G "http://concrete.test/api/tickets_contractor" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"start_date":"non","end_date":"officiis","ticket_number":"voluptas"}'

```

```javascript
const url = new URL(
    "http://concrete.test/api/tickets_contractor"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "start_date": "non",
    "end_date": "officiis",
    "ticket_number": "voluptas"
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "success": false,
    "error": "Authentication Error"
}
```

### HTTP Request
`GET api/tickets_contractor`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `start_date` | String |  optional  | optional Start date for ticket filter.
        `end_date` | String |  optional  | optional End date for ticket filter.
        `ticket_number` | String |  optional  | optional Ticket number to find.
    
<!-- END_b5da8b29b47755a2eb3691637cc99826 -->

<!-- START_a15e713e88d9ca29c96f861e9612a3ac -->
## Create ticket
Create a ticket to verify

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X POST \
    "http://concrete.test/api/tickets_contractor/create" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"number":"quia","date_gen":"ut","vehicle":"dolorem","material":"eum","other_material":"vitae","pickup":"nulla","deliver":"aspernatur","tonage":"eius","rate":"assumenda","total":"eos","photo":"expedita","photo_box_data":"vitae","photo_box_name":"eius"}'

```

```javascript
const url = new URL(
    "http://concrete.test/api/tickets_contractor/create"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "number": "quia",
    "date_gen": "ut",
    "vehicle": "dolorem",
    "material": "eum",
    "other_material": "vitae",
    "pickup": "nulla",
    "deliver": "aspernatur",
    "tonage": "eius",
    "rate": "assumenda",
    "total": "eos",
    "photo": "expedita",
    "photo_box_data": "vitae",
    "photo_box_name": "eius"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/tickets_contractor/create`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `number` | String |  required  | Ticket Number
        `date_gen` | Date |  required  | Ticket generation date format YYYY-MM-DD
        `vehicle` | String |  required  | Unit number or Alias of vechile
        `material` | String |  optional  | Material id or with value "other"
        `other_material` | String |  optional  | Required if material is "other"
        `pickup` | String |  optional  | Ticket Pickup
        `deliver` | String |  optional  | Ticket Deliver
        `tonage` | Decimal |  required  | Ticket Tonage
        `rate` | Decimal |  required  | Ticket Rate
        `total` | Decimal |  required  | Ticket Total by default is rate multiplied by tonage
        `photo` | File |  optional  | Ticket photo in file format
        `photo_box_data` | String |  optional  | Ticket photo in base64
        `photo_box_name` | String |  optional  | Ticket photo name, if it sended by base64
    
<!-- END_a15e713e88d9ca29c96f861e9612a3ac -->

<!-- START_43a27bc8c19b7968555adb773887bbfd -->
## Update ticket
Update ticket if state is sended to recheck or to verify

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X PUT \
    "http://concrete.test/api/tickets_contractor/update/reiciendis" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"number":"unde","date_gen":"quis","vehicle":"voluptas","material":"et","other_material":"quis","pickup":"neque","deliver":"molestiae","tonage":"ut","rate":"rerum","total":"officia","photo":"sit","photo_box_data":"et","photo_box_name":"occaecati"}'

```

```javascript
const url = new URL(
    "http://concrete.test/api/tickets_contractor/update/reiciendis"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "number": "unde",
    "date_gen": "quis",
    "vehicle": "voluptas",
    "material": "et",
    "other_material": "quis",
    "pickup": "neque",
    "deliver": "molestiae",
    "tonage": "ut",
    "rate": "rerum",
    "total": "officia",
    "photo": "sit",
    "photo_box_data": "et",
    "photo_box_name": "occaecati"
}

fetch(url, {
    method: "PUT",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/tickets_contractor/update/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  optional  | Integer required ID of ticket to modify.
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `number` | String |  required  | Ticket Number
        `date_gen` | Date |  required  | Ticket generation date format YYYY-MM-DD
        `vehicle` | String |  required  | Unit number or Alias of vechile
        `material` | String |  optional  | Material id or with value "other"
        `other_material` | String |  optional  | Required if material is "other"
        `pickup` | String |  optional  | Ticket Pickup
        `deliver` | String |  optional  | Ticket Deliver
        `tonage` | Decimal |  required  | Ticket Tonage
        `rate` | Decimal |  required  | Ticket Rate
        `total` | Decimal |  required  | Ticket Total by default is rate multiplied by tonage
        `photo` | File |  optional  | Ticket photo in file format
        `photo_box_data` | String |  optional  | Ticket photo in base64
        `photo_box_name` | String |  optional  | Ticket photo name, if it sended by base64
    
<!-- END_43a27bc8c19b7968555adb773887bbfd -->

<!-- START_d6c74f99b225e5f2201ef212b86d2214 -->
## Collection of payments
Shows all payments by contractor

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET \
    -G "http://concrete.test/api/payments" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"start_date":"nihil","end_date":"incidunt"}'

```

```javascript
const url = new URL(
    "http://concrete.test/api/payments"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "start_date": "nihil",
    "end_date": "incidunt"
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (401):

```json
{
    "success": false,
    "error": "Authentication Error"
}
```

### HTTP Request
`GET api/payments`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `start_date` | String |  optional  | optional Start date for payments filter (Format: YYYY-MM-DD).
        `end_date` | String |  optional  | optional End date for payments filter (Format: YYYY-MM-DD).
    
<!-- END_d6c74f99b225e5f2201ef212b86d2214 -->


