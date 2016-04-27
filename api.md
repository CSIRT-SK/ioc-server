# Public API

Accessible via POST and GET at `/api/index.php` or simply `/api/`.

General syntax:

* `controller` - controller name
* `action` - action name
* params - additional parameters based on action

GET example:

`/api/?controller=client&action=request&name=test`

## Client controller

* `request` - request an IOC definition set
  * `name` - name of definition set
* `upload` - upload results
  * `report` - results in JSON format

### Report format

```json
{
  "org": "org",
  "device": "dev",
  "timestamp": 1461740751,
  "setname": "test",
  "indicators": [
    {
      "id": 1,
      "result": 0
    },
    {
      "id": 2,
      "result": 1
    }
  ]
}
```
