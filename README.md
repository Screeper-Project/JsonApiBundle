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