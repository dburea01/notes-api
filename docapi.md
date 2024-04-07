# About NOTES-Api

NOTES-APi allows you to create “text” notes for organizations. **Honestly, it's no use**. It just allowed me to learn how to properly build APIs with the [Laravel framework](https://laravel.com/). And to understand all the necessary steps until the production.

## The authentication to use these endpoints

All the endpoints require an authentication (except the /register).

To authenticate, you can use this endpoint : /login
You will receive a response containing a bearer token. Example : 

```
{
    "token": "4|LXDnRNnZlDGzyHT0urHB4quuswVa5rGf5xD4pF0M0331f0b4",
    "user": {
        "id": "9bb3b1ba-1850-4791-a26e-c1ac91bde3b8",
        "role_id": "USER",
        "full_name": "LAST NAME First name",
        "organization_id": "9ba9b105-c56b-4537-86c0-76b0034d1c41"
    }
}
```

## Use the endpoints with the bearer token

Once you have your token, you can use endpoints this way:
```
curl --location 'localhost:8000/api/v1/organizations/9ba9b105-c56b-4537-86c0-76b0034d1c41/notes' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 4|LXDnRNnZlDGzyHT0urHB4quuswVa5rGf5xD4pF0M0331f0b4' \
--data '{
    "note" : "ma belle note",
    "background_color" : "#EEFFFF"
}'
```

## The translations
You can specify a translation language for error messages. In this way (example in "fr")

```
curl --location 'localhost:8000/api/v1/organizations/9ba9b105-c56b-4537-86c0-76b0034d1c41/notes' \
--header 'Accept: application/json' \
--header 'Accept-Language: fr' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 4|LXDnRNnZlDGzyHT0urHB4quuswVa5rGf5xD4pF0M0331f0b4' \
--data '{
    "note" : "ma belle note",
    "background_color" : "toto"
}'
```

The response will be :
```
{
    "message": "La couleur de fond n'est pas valide (doit etre une valeur hex)",
    "errors": {
        "background_color": [
            "La couleur de fond n'est pas valide (doit etre une valeur hex)"
        ]
    }
}
```

Same example in english :
```
curl --location 'localhost:8000/api/v1/organizations/9ba9b105-c56b-4537-86c0-76b0034d1c41/notes' \
--header 'Accept: application/json' \
--header 'Accept-Language: en' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 4|LXDnRNnZlDGzyHT0urHB4quuswVa5rGf5xD4pF0M0331f0b4' \
--data '{
    "note" : "ma belle note",
    "background_color" : "toto"
}'
```

The response will be : 
```
{
    "message": "The background color is not an hex value",
    "errors": {
        "background_color": [
            "The background color is not an hex value"
        ]
    }
}
```

Without "Accept-Language" , the default language will be applied (see you config.app file)



# I'm learning
I spent a bit of time learning though, so it's definitely not perfect. So if you have any suggestions for me, please let me know. https://github.com/dburea01/notes-api/issues

# License

You can use it, clone, copy/paste, etc...
