# City-Link Express Tracking API
Return JSON formatted string of City-Link Express Tracking details

# Installation
```composer require afzafri/city-link-express-tracking-api:dev-master```

# Usage
- ```http://site.com/api.php?trackingNo=CODE```
- where ```CODE``` is your parcel tracking number
- It will then return a JSON formatted string, you can parse the JSON string and do what you want with it.

# Sample Response
```yaml
{
    "http_code": 200,
    "error_msg": "No error",
    "status": 1,
    "message": "Record Found",
    "data": [
        {
            "date": "07/05/2020",
            "time": "08:25 PM",
            "location": "NILAI, MALAYSIA",
            "process": "Package Delivered - Signed for by:XXX"
        },
        {
            "date": "07/05/2020",
            "time": "02:06 PM",
            "location": "NILAI, MALAYSIA",
            "process": "With City-Link Delivery Courier: XXX"
        },
        {
            "date": "06/05/2020",
            "time": "09:38 AM",
            "location": "NILAI, MALAYSIA",
            "process": "At City-Link Local Facility"
        },
        {
            "date": "06/05/2020",
            "time": "05:56 AM",
            "location": "DATARAN CITY-LINK EXPRESS, MALAYSIA",
            "process": "Departed sorting facility"
        },
        {
            "date": "05/05/2020",
            "time": "09:18 PM",
            "location": "SHAMELIN PERKASA, MALAYSIA",
            "process": "Departed City-Link Facility"
        },
        {
            "date": "05/05/2020",
            "time": "09:01 PM",
            "location": "SHAMELIN PERKASA, MALAYSIA",
            "process": "Arrived City-Link Facility"
        }
    ],
    "info": {
        "creator": "Afif Zafri (afzafri)",
        "project_page": "https://github.com/afzafri/City-Link-Express-Tracking-API",
        "date_updated": "09/12/2020"
    }
}
```

# License
This library is under ```MIT license```, please look at the LICENSE file
