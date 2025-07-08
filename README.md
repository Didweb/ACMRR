# ACMRR

# Test

Ejecutar test dentro del contendor.

Entrar en el contendor:

```bash
docker exec -it acmrr-php bash
```

Ejecutar todos los test...

```bash
APP_ENV=test ./vendor/bin/phpunit  --coverage-html coverage/ 
```

Por suites:

```bash
./vendor/bin/phpunit --testsuite Unitario
```
```bash
./vendor/bin/phpunit --testsuite Funcional
```

```bash
./vendor/bin/phpunit --testsuite Dto
```

```bash
./vendor/bin/phpunit --testsuite Entity
```

### Ver el coverage

```bash
http://localhost/coverage/index.html
```
