# ACMRR

# Test

Ejecutar test dentro del contendor.

Entrar en el contendor:

```bash
docker exec -it acmrr-php bash
```

Ejecutar todos los test...

```bash
./vendor/bin/phpunit
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
