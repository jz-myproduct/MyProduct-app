bin/console doctrine:schema:drop -n -q --force --full-database
rm migrations/*.php
bin/console make:migration
bin/console doctrine:migrations:migrate -n -q
bin/console doctrine:fixtures:load

