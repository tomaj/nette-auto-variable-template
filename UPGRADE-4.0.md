# Upgrading from 3.x to 4.0

4.0 keeps the configuration of 3.x: the `config.neon` service definition, `registerVariable()`, `getVariables()` and `onCreate` work as before. The breaking changes are about the platform and the class itself.

## PHP 8.4 or newer

4.0 requires PHP 8.4 (`"php": "^8.4"`; 3.x allowed PHP 8.1+). Stay on 3.x until you run a supported PHP version.

Before (`composer.json`):

```json
"require": {
    "tomaj/nette-auto-variable-template": "^3.0"
}
```

After:

```json
"require": {
    "php": "^8.4",
    "tomaj/nette-auto-variable-template": "^4.0"
}
```
