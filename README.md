## DISCONTINUED AS I AM NOT PLAYING WORLD OF WARCRAFT ANYMORE. PLEASE CONTACT ME IF YOU WANT TO CONTINUE DEVELOPING.

# BattleREST
Parsing Data from the Blizzard API

## USAGE:

```php
<?php

require 'PATH TO FILES/rest.php';

$battleRest = new REST();

$battleRest->query = "CHARACTER <character> FROM <realm> <extrafields>";

echo $battleRest->query;
```

---
## Configuration Extras(optional):
---
```php

$battleRest->sslSupport = true;
$battleRest->apcCaching = true;
$battleRest->memCached = true;
$battleRest->region = "eu";
$battleRest->authentication = true;
$battleRest->authToken = "YOURAUTHTOKENHERE";
```
---

or change the values at the top of the lib/REST/REST.php file
