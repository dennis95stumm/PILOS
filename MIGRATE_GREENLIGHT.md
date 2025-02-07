# Migrate from Greenlight to PILOS

PILOS provides an easy to use command to import all greenlight users (incl. ldap), rooms and shared accesses.

## Preparing migration from other host

If you plan to run PILOS on a different host than greenlight, you have to adjust the docker-compose.yml inside the greenlight directory
to publish the database port.

Change the port configuration for the db service from:

```
ports:
- 127.0.0.1:5432:5432
```
to:

```
ports:
- 5432:5432
```

Also make sure the internal firewall of the OS and no external firewall is not blocking access to the port and from the host PILOS is running on.

## Running migration command

Before running the command make sure your ldap configuration is valid.

```
php artisan import:greenlight   {host : ip or hostname of postgres database server}
                                {port : port of postgres database server}
                                {database : greenlight database name, see greenlight .env variable DB_NAME}
                                {username : greenlight database username, see greenlight .env variable DB_USERNAME}
                                {password : greenlight database password, see greenlight .env variable DB_PASSWORD}                                                 
```

**Example**

```
php artisan import:greenlight localhost 5432 greenlight_production postgres 12345678
```

The command will output the process of the import and imforms about failed user, room and shared access import.

**Note** If a room with the same room id already exists in PILOS it will NOT be imported and the shared accesses are ignored.

## Adjust nginx to redirect to PILOS (other host)

Please replace the following section of the greenlight nginx configuration:

```
location /b {
  proxy_pass          http://127.0.0.1:5000;
  proxy_set_header    Host              $host;
  proxy_set_header    X-Forwarded-For   $proxy_add_x_forwarded_for;
  proxy_set_header    X-Forwarded-Proto $scheme;
  proxy_http_version  1.1;
}

location /b/cable {
  proxy_pass          http://127.0.0.1:5000;
  proxy_set_header    Host              $host;
  proxy_set_header    X-Forwarded-For   $proxy_add_x_forwarded_for;
  proxy_set_header    X-Forwarded-Proto $scheme;
  proxy_set_header    Upgrade           $http_upgrade;
  proxy_set_header    Connection        "Upgrade";
  proxy_http_version  1.1;
  proxy_read_timeout  6h;
  proxy_send_timeout  6h;
  client_body_timeout 6h;
  send_timeout        6h;
}

# Allow larger body size for uploading presentations
location ~ /preupload_presentation$ {
  client_max_body_size 30m;

  proxy_pass          http://127.0.0.1:5000;
  proxy_set_header    Host              $host;
  proxy_set_header    X-Forwarded-For   $proxy_add_x_forwarded_for;
  proxy_set_header    X-Forwarded-Proto $scheme;
  proxy_http_version  1.1;
}

# Only needed if using presentations and deployed at a relative root (ex "/b")
# If deploying at "/", delete the section below

location /rails/active_storage {
  return 301 /b$request_uri;
}
```

with

```
location /b {
    return 301 https://DOMAIN.TLD$request_uri;
}
```

This will redirect all traffic to PILOS. If you don't use the /b base path for greenlight adjust the code accordingly.
Replace **DOMAIN.TLD** with the hostname of your PILOS installation.

## Enable greenlight compatibility mode

To enable support for the most common greenlight urls eg. room urls, /ldap_signin, /signin and /default_room please set the following .env variable
```
GREENLIGHT_COMPATIBILITY=true
```

If your greenlight base was different than /b adjust the .env variable. Do not include the slash "/".
```
GREENLIGHT_PATH=b
```
**Note** We don't support greenlight compatibility mode for greenlight installations without a prefix like /b.
You also need to make sure the prefix does not collide with PILOS urls (check routes in: routes/api.php, routes/web.php and resources/js/router.js )

## Shutdown greenlight

To shutdown greenlight run this command inside the greenlight directory
```
docker-compose down
```
