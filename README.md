Graille-Labs JsonApi bundle
=====================

The JsonApi bundle add support of JsonApi (plugin of alecgorge) in symfony 2.
The github of JsonApi : https://github.com/alecgorge/jsonapi
The webpage of JsonApi : Not available

Installation
============
Add :

```
"graille-labs/jsonapi-bundle": "dev-master"
```

Configuration
=============
In the app/config/config.yml :

```
gl_json_api:
    servers:
		## Your servers
```

You can add many servers :

```
json_api:
    servers:
        default: ## The "default" server is required
            login: #username
            password: #password
            port: #port
            ip: #ip
            salt: ~
        serv1:
            login: #username
            password: #password
            port: #port
            ip: #ip
            salt: ~
```

N.B : Port and Salt are optionnal, the port by default is 20059

If you need to copy a server, you can create a pattern :

```
json_api:
    servers:
        default: ## The "default" server is required
            pattern: serv1 ## Default server is "serv1"
        serv1:
            login: #username
            password: #password
            port: #port
            ip: #ip
            salt: ~
```

You can erase the configuration of a pattern :
```
json_api:
    servers:
        default: ## The "default" server is required
            pattern: serv1 ## Default server is "serv1"
        serv1:
            pattern: serv2
            ip: #new_ip
        serv2:
            login: #username
            password: #password
            port: #port
            ip: #ip
            salt: ~
```

(In this example, the informations are the same, but the ip isn't.)

Usage
=============

For use, you must call the service :

```
$api = $this->container->get('gl.jsonapi.api')->getApi("servername");
```

After that, you can use the api normally.
If "servername" is empty, the default server will be used.