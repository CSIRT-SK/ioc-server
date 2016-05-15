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

### IOC format

Whole definition is a list of IOCs:
```json
[
  <ioc>,
  ...
]
```
`<ioc>` can be either `<simple>` or `<logic>`

`<simple>`:
```json
{
  "id": 1,
  "name": "name",
  "type": "type",
  "value": [
    "value",
    ...
  ]
}
```

`<logic>`:
```json
{
  "id": 2,
  "name": "#2",
  "type": "and",
  "children": [
    <ioc>,
    ...
  ]
}
```

### Report format

```json
{
  "org": "org",
  "dev": "dev",
  "timestamp": 1461740751,
  "set": "test",
  "results": [
    {
      "id": 1,
      "result": 0,
      "data": []
    },
    {
      "id": 2,
      "result": 1,
      "data": ["data1", "data2"]
    }
  ]
}
```
