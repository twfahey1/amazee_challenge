docker-compose exec cli vendor/bin/phpunit \
  -c /app/phpunit.xml \
  --verbose \
  --debug \
  web/modules/custom/ultimate_lexparser/tests/src/Unit/ParseTest.php